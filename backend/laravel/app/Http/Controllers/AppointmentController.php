<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller

{

    public function index()
    {
        return response()->json(
            Appointment::with('client')
                ->where('user_id', auth()->id())
                ->get()
        );
    }
  

   
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'in:geplant,erledigt,abgesagt',
        ]);

        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'client_id' => $request->client_id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'location' => $request->location,
            'status' => $request->status,
            'category' => $request->category,
        ]);

        return response()->json($appointment, 201);
    }

   
    public function show($id)
    {
        $appointment = Appointment::where('user_id', auth()->id())->findOrFail($id);
        return response()->json($appointment);
    }

 
    public function update(Request $request, $id)
    {
        $appointment = Appointment::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'time' => 'sometimes',
            'status' => 'sometimes|in:geplant,erledigt,abgesagt',
        ]);

        $appointment->update($request->all());
        return response()->json($appointment);
    }

  
    public function destroy($id)
    {
        $appointment = Appointment::where('user_id', auth()->id())->findOrFail($id);
        $appointment->delete();
        return response()->json(['message' => 'Termin erfolgreich gelöscht']);
    }

    //Bericht speichern

    public function saveReport(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = auth()->user();
    
        $workedHours = $request->input('worked_hours');
        $reportText = $request->input('report');
        $cost = $workedHours * $user->hourly_rate;
    
      
        $appointment->update([
            'worked_hours' => $workedHours,
            'cost' => $cost,
            'report' => $reportText,
            'billed' => 0 // Noch nicht fakturiert
        ]);
    
        // Gesamtstunden & Einnahmen des Mitarbeiters aktualisieren
        $user->increment('total_hours', $workedHours);
        $user->increment('total_earnings', $cost);
    
        return response()->json(['message' => 'Bericht gespeichert']);
    }

    public function getReport($id)
{
    $appointment = Appointment::findOrFail($id);

    return response()->json([
        'worked_hours' => $appointment->worked_hours,
        'report' => $appointment->report,
        'cost' => $appointment->cost,
        'billed' => $appointment->billed
    ]);
}
}
