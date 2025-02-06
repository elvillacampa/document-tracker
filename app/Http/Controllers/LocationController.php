<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'location' => 'required|string',
            'receiver' => 'required|string',
            'timestamp' => 'required|date',
        ]);
    

        $location = Location::create([
            'document_id' => $request->document_id,
            'location' => $request->location,
            'receiver' => $request->receiver,
            'timestamp' => $request->timestamp,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        return redirect()->route('documents.index')->with('success', 'Location added successfully!');
    
    }
    
    
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'receiver' => 'required|string',
            'timestamp' => 'required|date',
        ]);
    
        $location->update($validatedData);
        $location->update([
            ...$validatedData,
            'updated_by' => auth()->id(),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Routing history updated successfully!',
            'location' => $location
        ]);
    }
    
    
    
    public function destroy(Request $request, $id)
    {
        try {
            $location = Location::findOrFail($id); // Ensure the location exists
            $location->delete(); // Delete the location
    
            return response()->json([
                'success' => true,
                'message' => 'Routing history deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete routing history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
}
