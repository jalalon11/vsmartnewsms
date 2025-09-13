<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the feedback.
     */
    public function index(Request $request)
    {
        $query = Feedback::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%");
            });
        }

        $feedback = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Feedback/Index', [
            'feedback' => $feedback,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'service_type' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'rating' => $request->rating,
            'service_type' => $request->service_type,
            'is_featured' => false
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback! We appreciate your input.'
            ]);
        }

        return back()->with('success', 'Thank you for your feedback! We appreciate your input.');
    }

    /**
     * Toggle the featured status of feedback.
     */
    public function toggleFeatured(Feedback $feedback)
    {
        $feedback->update([
            'is_featured' => !$feedback->is_featured
        ]);

        return back()->with('success', 'Feedback status updated successfully.');
    }

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return back()->with('success', 'Feedback deleted successfully.');
    }

    /**
     * Display the specified feedback for sharing.
     */
    public function share(Feedback $feedback)
    {
        // Check if user is authenticated for admin access
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return Inertia::render('Feedback/Show', [
            'feedback' => $feedback->load('user')
        ]);
    }

    /**
     * Display the specified feedback.
     */
    public function show(Feedback $feedback)
    {
        return Inertia::render('Feedback/Show', [
            'feedback' => $feedback->load('user')
        ]);
    }
}