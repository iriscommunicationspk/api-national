<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserRequestModel;

class UserRequestController extends Controller
{
    public function submitRequest(Request $request)
    {
        //
        $request->validate([
            'date' => 'required|date',
            'department' => 'required|string',
            'name' => 'required|string',
            'researchRequirement' => 'required|string',
            'scope' => 'required|string',
        ]);

        $userRequest = UserRequestModel::create($request->all());

        return response()->json([
            'message' => 'Request submitted successfully',
            'request' => $userRequest,
        ]);
    }



    public function updateRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_requests,id',
            'date' => 'required|date',
            'department' => 'required|string',
            'name' => 'required|string',
            'researchRequirement' => 'required|string',
            'scope' => 'required|string',
        ]);

        $userRequest = UserRequestModel::where('id', $request->id)->update($request->all());

        return response()->json([
            'message' => 'Request updated successfully',
            'request' => $userRequest,
        ]);
    }

    public function deleteRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_requests,id',
        ]);

        $userRequest = UserRequestModel::where('id', $request->id)->delete();

        return response()->json([
            'message' => 'Request deleted successfully',
            'request' => $userRequest,
        ]);
    }

    public function approveRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_requests,id',
            'adminResponse' => 'required|string',
        ]);

        $userRequest = UserRequestModel::where('id', $request->id)->update(['status' => 'approved', 'adminResponse' => $request->adminResponse]);

        return response()->json([
            'message' => 'Request approved successfully',
            'request' => $userRequest,
        ]);
    }

    public function rejectRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_requests,id',
            'adminResponse' => 'required|string',
        ]);

        $userRequest = UserRequestModel::where('id', $request->id)->update(['status' => 'rejected', 'adminResponse' => $request->adminResponse]);

        return response()->json([
            'message' => 'Request rejected successfully',
            'request' => $userRequest,
        ]);
    }

    public function getAllRequests(Request $request)
    {
        $requests = UserRequestModel::all();
        return response()->json([
            'requests' => $requests,
        ]);
    }
    public function getRequestById(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user_requests,user_id',
        ]);
        $request = UserRequestModel::where('user_id', $request->user_id)->get();
        return response()->json([
            'request' => $request,
        ]);
    }
}
