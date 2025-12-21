<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;

class BlogController extends Controller
{
   public function index()
{
    $media = Media::all(); // Fetch all media items
    return view('blog.index', compact('media'));
}

}



