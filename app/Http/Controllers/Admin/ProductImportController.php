<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductImportController extends Controller
{
    /**
     * Show the import form.
     */
    public function showForm()
    {
        return view('admin.products.import');
    }

    /**
     * Process the uploaded Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new ProductsImport();
            Excel::import($import, $request->file('file'));

            $message = "Import completed! {$import->successCount} products imported successfully.";
            
            if ($import->errorCount > 0) {
                $message .= " {$import->errorCount} rows had errors.";
            }

            return redirect()
                ->route('admin.products.import')
                ->with('success', $message)
                ->with('errors_list', $import->errors);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.products.import')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Download the Excel template.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_import_template.csv"',
        ];

        $columns = [
            'name',
            'description', 
            'express_price',
            'standard_price',
            'shipping_type',
            'stock',
            'category',
            'brand',
        ];

        // Sample data rows
        $sampleData = [
            [
                'Nivea Body Lotion 400ml',
                'Moisturizing body lotion for dry skin with vitamin E',
                '5000',
                '4500',
                'both',
                '25',
                'Skincare',
                'Nivea',
            ],
            [
                'L\'Oreal Paris Shampoo 250ml',
                'Professional hair care shampoo for all hair types',
                '8500',
                '',
                'express_only',
                '15',
                'Hair Care',
                'L\'Oreal',
            ],
            [
                'Maybelline Lipstick Red',
                'Long-lasting matte lipstick in classic red',
                '12000',
                '10000',
                'both',
                '30',
                'Makeup',
                'Maybelline',
            ],
        ];

        $callback = function () use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // Write header row
            fputcsv($file, $columns);
            
            // Write sample data
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
