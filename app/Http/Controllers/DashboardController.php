<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Pledge;
use App\Models\Contribution;
use App\Models\Invitation;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $totalPledgeAmount = Pledge::sum('amount'); 
        $totalContributionAmount = Contribution::sum('amount'); 

        return view('dashboard', [
            'eventsCount' => Event::count(),
            'guestsCount' => Guest::count(),
            'pledgesCount' => Pledge::count(),
            'contributionsCount' => Contribution::count(),
            'invitationsCount' => Invitation::count(),
            'totalPledgeAmount' => $totalPledgeAmount,
            'totalContributionAmount' => $totalContributionAmount
        ]);
    }
}