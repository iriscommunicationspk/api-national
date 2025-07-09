<?php

namespace App\Http\Controllers;

use App\Models\EventsModel;
use App\Models\User;
use App\Models\UsersEventsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    public function getEvents(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        $events = EventsModel::all();
        if ($events->count() < 1) {
            return response()->json(['result' => 'success', 'message' => 'No record.'], 420);
        }

        return response()->json(['result' => 'success', 'events' => $events], 200);
    }

    public function addEvent(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        // Check if user is admin (role 2 based on the seeder)
        if ($user->role != 2) {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date|after:today',
            'link' => 'nullable|url',
        ]);
        $validated['is_active'] = 1;

        try {
            $event = EventsModel::create($validated);
            if ($event) {
                return response()->json(['result' => 'success', 'event' => $event], 200);
            }

            return response()->json(['result' => 'failed', 'message' => 'Event not entered.'], 420);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while creating the event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateEvent(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        // Check if user is admin
        if ($user->role != 2) {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        $validated = $request->validate([
            'id' => 'required'
        ]);

        $event = EventsModel::find($validated['id']);
        if (!$event) {
            return response()->json(['result' => 'success', 'message' => 'Incorrect event id.'], 422);
        }

        $data = $request->all();
        unset($data['id']);

        if (empty($data)) {
            return response()->json([
                'message' => 'No data to update.',
            ], 422);
        }

        if ($event->update($data)) {
            return response()->json(['result' => 'success', 'event' => $event], 200);
        }

        return response()->json([
            'result' => 'failed',
            'message' => 'Unable to update event.',
        ], 500);
    }

    public function deleteEvent(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        // Check if user is admin
        if ($user->role != 2) {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        $validated = $request->validate([
            'id' => 'required|exists:events,id'
        ]);

        $event = EventsModel::find($validated['id']);
        if (!$event) {
            return response()->json(['result' => 'failed', 'message' => 'Event not found.'], 422);
        }

        try {
            // Delete related user_events first (cascade delete)
            $event->users()->detach();

            // Delete the event
            $event->delete();

            return response()->json(['result' => 'success', 'message' => 'Event deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'failed',
                'message' => 'Failed to delete event.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function participateInEvent(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participate' => 'required|boolean'
        ]);

        $event = EventsModel::find($validated['event_id']);
        if (!$event) {
            return response()->json(['result' => 'failed', 'message' => 'Event not found.'], 422);
        }

        // Check if user already has a participation record for this event
        $existingParticipation = UsersEventsModel::where('user_id', $user->id)
            ->where('event_id', $validated['event_id'])
            ->first();

        if ($validated['participate']) {
            // User wants to participate
            if (!$existingParticipation) {
                UsersEventsModel::create([
                    'user_id' => $user->id,
                    'event_id' => $validated['event_id']
                ]);
                return response()->json(['result' => 'success', 'message' => 'Successfully registered for the event.'], 200);
            } else {
                return response()->json(['result' => 'success', 'message' => 'Already registered for this event.'], 200);
            }
        } else {
            // User doesn't want to participate, remove if exists
            if ($existingParticipation) {
                $existingParticipation->delete();
                return response()->json(['result' => 'success', 'message' => 'Successfully unregistered from the event.'], 200);
            } else {
                return response()->json(['result' => 'success', 'message' => 'Not registered for this event.'], 200);
            }
        }
    }

    public function getUserParticipationStatus(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        $validated = $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $participation = UsersEventsModel::where('user_id', $user->id)
            ->where('event_id', $validated['event_id'])
            ->exists();

        return response()->json([
            'result' => 'success',
            'participating' => $participation
        ], 200);
    }

    public function userEvents(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        $validated = $request->validate([
            'user_id' => 'required'
        ]);

        $user = User::with(
            'events'
        )->find($validated['user_id']);

        if (!$user) {
            return response()->json(['result' => 'success', 'message' => 'User not found with provided id.'], 422);
        }

        return response()->json(['result' => 'success', 'user' => $user], 200);
    }

    public function eventUsers(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        // Check if user is admin for viewing event participants
        if ($user->role != 2) {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        $validated = $request->validate([
            'event_id' => 'required'
        ]);


        $event = EventsModel::with(
            'users'
        )->find($validated['event_id']);

        if (!$event) {
            return response()->json(['result' => 'success', 'message' => 'Event not found with provided id.'], 422);
        }

        return response()->json(['result' => 'success', 'event' => $event], 200);
    }


    public function eventUsersAll(Request $request)
    {

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized user.'], 422);
        }

        // Check if user is admin
        if ($user->role != 2) {
            return response()->json(['message' => 'Access denied. Admin privileges required.'], 403);
        }

        $events = EventsModel::with('users')->get();
        if (!$events) {
            return response()->json(['result' => 'success', 'message' => 'No event found.'], 422);
        }

        return response()->json(['result' => 'success', 'events' => $events], 200);
    }
}
