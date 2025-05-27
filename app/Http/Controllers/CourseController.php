<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        $course = Course::create([
            'course_name' => $request->course_name,
            'price' => $request->price,
        ]);

        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course,
        ], 201);
    }

    // Get all available courses
    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
    }
}

