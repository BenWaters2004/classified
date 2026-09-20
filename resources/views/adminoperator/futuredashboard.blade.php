@extends('layout.admin')

@section('title', "Admin - Dashboard")

@section('sidebar')
@endsection

@section('content')

@inject('notifications', 'App\Http\Controllers\Notifications')

@php  
$userNotifications = $notifications->getHeaderNotifications()->take(10);
@endphp

@inject('checkAccess', 'App\Http\Controllers\Controller')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <h2>Hi {{ \Auth::user()->firstName }} {{\Auth::user()->lastName}}, Welcome Back!</h2>
        <div class="dashboard-date">
            <p id="current-date"></p>
            <p id="current-time"></p>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="dashboard-cards">
        <!-- Row 1 -->
        <!-- Card 1: Application Status -->
        <div class="card status-card">
            <div class="status-details">
                <div class="status-item">
                    <span class="status-badge red">{{$pendingAndAwaiting}}</span>
                    <p class="lable"><a href="{{ env('APP_URL') }}applications/pendingRequests">Pending Registration</a></p>
                </div>
                <div class="status-item">
                    <span class="status-badge orange">{{$incompleteUsers}}</span>
                    <p class="lable"><a href="{{ env('APP_URL') }}applications/inprogressApplications">Applications in progress</a></p>
                </div>
                <div class="status-item">
                    <span class="status-badge green">{{$completedUsers}}</span>
                    <p class="lable"><a href="{{ env('APP_URL') }}applications/completedApplications">Applications completed</a></p>
                </div>
            </div>
            <div class="status-chart">
                <canvas class="pieGraph" id="applicationStatusChart"></canvas>
            </div>
        </div>

        <div class="card">
            @if ($checkAccess->checkAccess('superuser'))
                <p class="organisationName"><strong>All Organisations</strong></p>
                <p>Total Candidates Processed: <strong>{{ $currentUser->totalCompleted }}</strong></p>
                <p>Candidates Processed this month: <strong>{{ $completedThisMonthCount }}</strong></p>
                <p class="btm-org-stat">Candidates Processed last month: <strong>{{ $completedLastMonthCount }}</strong></p>
                
                <!-- Growth Section Positioned Bottom Right -->
                <div class="growth-container">
                    <span class="growth-percentage {{ $growthClass }}">{{ $percentageChange }}% {{ $growthSymbol }}</span>
                    <p>This month compared to last month</p>
                </div>
            @else
                <p class="organisationName"><strong>{{ $currentUser->organisationName }}</strong></p>
                <p>Total Candidates Processed: <strong>{{ $currentUser->totalCompleted }}</strong></p>
                <p>Candidates Processed this month: <strong>{{ $completedThisMonthCount }}</strong></p>
                <p class="btm-org-stat">Candidates Processed last month: <strong>{{ $completedLastMonthCount }}</strong></p>
                
                <!-- Growth Section Positioned Bottom Right -->
                <div class="growth-container">
                    <span class="growth-percentage {{ $growthClass }}">{{ $percentageChange }}% {{ $growthSymbol }}</span>
                    <p>This month compared to last month</p>
                </div>
            @endif
        </div>

        <!-- Manage Candidates Section -->
        <div class="card manage-candidates">
            <a href="{{ env('APP_URL') }}adminoperator/newApplicationRequest" class="btn btn-dashboard">Add New Candidate</a>
            <a href="{{ env('APP_URL') }}applications/allApplications" class="btn btn-dashboard">Search All Candidates</a>
            <a href="{{ env('APP_URL') }}applications/pendingRequests" class="btn btn-dashboard">Pending Registration</a>
            <a href="{{ env('APP_URL') }}applications/inprogressApplications" class="btn btn-dashboard">Applications In Progress</a>
            <a href="{{ env('APP_URL') }}applications/completedApplications" class="btn btn-dashboard">Completed Applications</a>
        </div>

        <!-- Row 2 -->
        <div class="card purge-warning-card">
            <div class="purge-card-header">
                <div>
                    <h3>
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                        Upcoming Data Purges
                    </h3>

                    <p class="purge-card-subtitle">
                        Candidates scheduled for deletion at the next retention purge.
                    </p>
                </div>

                <span class="purge-count">
                    {{ isset($purgeCandidates) ? $purgeCandidates->count() : 0 }}
                </span>
            </div>

            @if(isset($purgeCandidates) && $purgeCandidates->count() > 0)

                <div class="purge-list">

                    @foreach($purgeCandidates as $candidate)

                        <a
                            href="{{ url('/finalReport/generate/' . $candidate->id) }}"
                            class="purge-candidate"
                            target="_blank"
                        >
                            <div class="purge-candidate-main">

                                <div class="purge-avatar">
                                    {{ strtoupper(substr($candidate->firstName ?? '', 0, 1)) }}
                                    {{ strtoupper(substr($candidate->lastName ?? '', 0, 1)) }}
                                </div>

                                <div class="purge-candidate-details">

                                    <strong>
                                        {{ $candidate->firstName }}
                                        {{ $candidate->lastName }}
                                    </strong>

                                    <span class="purge-email">
                                        {{ $candidate->email }}
                                    </span>

                                    <span class="purge-organisation">
                                        {{ $candidate->organisationName }}
                                    </span>

                                </div>

                            </div>

                            <div class="purge-candidate-date">

                                <span class="purge-date">
                                    {{ $candidate->purgeDateFormatted }}
                                </span>

                                <small>
                                    Retention expired
                                    {{ $candidate->retentionExpiresFormatted }}
                                </small>

                            </div>

                        </a>

                    @endforeach

                </div>

            @else

                <div class="purge-empty">

                    <i
                        class="fa fa-check-circle"
                        aria-hidden="true"
                    ></i>

                    <strong>No upcoming data purges</strong>

                    <span>
                        No candidates are currently due for deletion
                        at the next scheduled purge.
                    </span>

                </div>

            @endif

            <div class="purge-footer">

                <i
                    class="fa fa-clock-o"
                    aria-hidden="true"
                ></i>

                Next purge:

                <strong>
                    {{ isset($nextPurgeAt)
                        ? $nextPurgeAt->format('l d F Y \a\t H:i')
                        : 'Not scheduled'
                    }}
                </strong>
            </div>
        </div>
        <div class="card notifications-card">
            <h3>Notifications</h3>
            <div class="notifications-container">
                @if(isset($userNotifications) && count($userNotifications) > 0)
                    <ul class="notifications-list">
                        @foreach ($userNotifications as $notification)
                            <li class="notification-item">
                                <a href="{{ env('APP_URL') }}notifications/viewNotification/{{$notification->id}}">
                                    <div class="notification-icon">
                                        @if(isset($notification->category))
                                            @if(empty($notification->category)) 
                                                <i class="fa fa-info-circle text-primary"></i>
                                            @elseif($notification->category == 1) 
                                                <i class="fa fa-exclamation-triangle text-warning"></i>
                                            @elseif($notification->category == 2) 
                                                <i class="fa fa-bell text-danger"></i>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="notification-text">
                                        <p class="notification-title">{{ $notification->title ?? 'New Notification' }}</p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="no-notifications">
                        <i class="fa fa-bell-slash"></i>
                        <p>No new notifications</p>
                    </div>
                @endif
            </div>
            <div class="notifications-footer">
                <a href="{{ env('APP_URL') }}notifications/" class="view-all">View All Notifications</a>
            </div>
        </div>

        <div class="card application-duration-card" style="grid-column: span 2;">
            <h3>Application Duration</h3>
            <p>Time taken to complete applications</p>
            <canvas id="applicationDurationChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('pageCSS')
<style>
/* General Layout */
.dashboard-container {
    padding: 20px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    background: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #C55359;
}

.dashboard-date p {
    margin: 0;
    text-align: right;
    color: #555;
    font-size: 14px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

@media (max-width: 991px) {
    .dashboard-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

.card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.card .lable {
    position: relative;
    z-index: 1; /* Bring text to the foreground */
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    margin: 0;
}


/* Card 1: Application Status */
.status-card {
    grid-column: span 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-details {
    flex: 1;
}

.status-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.status-badge {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 18px;
    font-weight: bold;
    margin-right: 10px;
    color: #fff;
}

.status-badge.red {
    background-color: #ff4d4f;
}

.status-badge.orange {
    background-color: #ffa940;
}

.status-badge.green {
    background-color: #52c41a;
}

.status-chart {
    flex: 0.5;
    display: flex;
    justify-content: center;
}

.pieGraph {
    width: 200px;
    height: 200px;
}

@media (max-width: 565px) {
    .pieGraph {
        width: 150px;
        height: 150px;
    }
}
@media (min-width: 991px) and (max-width: 1222px){
    .pieGraph {
        width: 150px;
        height: 150px;
    }
}

/* Placeholder Card */
.placeholder-card {
    background-color: #e8e8e8;
    border: 2px dashed #ccc;
    height: 100%;
}

.manage-candidates {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 10px;
    padding: 15px;
}
@media (max-width: 445px) {
    .manage-candidates {
        padding: 5px;
    }
    .btm-org-stat {
        margin-bottom: 80px;
    }
}

.btn-dashboard {
    display: block;
    width: 100%;
    padding: 10px;
    text-align: center;
    background-color: #2c3c64;
    color: white;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn-dashboard:hover {
    background-color: #C55359;
    color: white;
}

.organisationName {
    color: #C55359;
    font-size: 18px;
}

/* Growth Container */
.growth-container {
    position: absolute;
    bottom: 10px;
    right: 15px;
    text-align: right;
}

.growth-percentage {
    font-size: 28px; /* Increase percentage size */
    font-weight: bold;
    display: block;
}

.growth-percentage.green {
    color: #52c41a; /* Green for positive growth */
}

.growth-percentage.red {
    color: #ff4d4f; /* Red for negative growth */
}

.growth-container p {
    font-size: 14px;
    margin: 0;
    color: #555;
}

/* Application Duration Chart */
.application-duration-card {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.application-duration-card h3 {
    color: #C55359;
    margin-bottom: 10px;
}

.application-duration-card canvas {
    max-width: 100%;
    height: auto;
}


/* Notifications Card */
.notifications-card {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
}

.notifications-card h3 {
    color: #C55359;
    font-size: 18px;
    margin-bottom: 15px;
    font-weight: bold;
}

.notifications-container {
    max-height: 340px;
    overflow-y: auto;
}

.notifications-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 5px;
    margin-bottom: 10px;
    transition: background 0.3s ease-in-out;
}

.notification-item:hover {
    background: #e8e8e8;
}

.notification-item a {
    display: flex;
    width: 100%;
    align-items: center;
    text-decoration: none;
    color: #333;
}

.notification-icon {
    font-size: 18px;
    margin-right: 10px;
}

.notification-text {
    flex: 1;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
}

/* No Notifications */
.no-notifications {
    text-align: center;
    padding: 20px;
    color: #888;
}

.no-notifications i {
    font-size: 24px;
    margin-bottom: 10px;
}

/* View All Notifications */
.notifications-footer {
    text-align: center;
    margin-top: 10px;
}

.view-all {
    font-size: 14px;
    color: #2c3c64;
    text-decoration: none;
    font-weight: bold;
    display: inline-block;
    padding: 8px 12px;
    border-radius: 4px;
    background: #e8e8e8;
    transition: background 0.3s;
}

.view-all:hover {
    background: #C55359;
    color: #fff;
}

.purge-warning-card {
    display: flex;
    flex-direction: column;
    min-height: 260px;
}

.purge-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 15px;
}

.purge-card-header h3 {
    margin: 0 0 5px 0;
    color: #2C3C64;
    font-size: 18px;
    font-weight: 600;
}

.purge-card-header h3 i {
    color: #C55359;
    margin-right: 5px;
}

.purge-card-subtitle {
    margin: 0;
    color: #777;
    font-size: 13px;
}

.purge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-width: 34px;
    height: 34px;

    padding: 0 10px;

    border-radius: 17px;

    background: #C55359;
    color: #fff;

    font-size: 15px;
    font-weight: 600;
}

.purge-list {
    flex: 1;

    max-height: 300px;

    overflow-y: auto;

    margin-left: -5px;
    margin-right: -5px;
    padding: 0 5px;
}

.purge-candidate {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    padding: 12px 0;

    color: inherit;
    text-decoration: none;

    border-bottom: 1px solid #eee;

    transition:
        background-color 0.15s ease,
        padding 0.15s ease;
}

.purge-candidate:last-child {
    border-bottom: 0;
}

.purge-candidate:hover,
.purge-candidate:focus {
    background: #fafafa;
    color: inherit;
    text-decoration: none;
}

.purge-candidate-main {
    display: flex;
    align-items: center;
    gap: 10px;

    min-width: 0;
}

.purge-avatar {
    width: 36px;
    height: 36px;

    flex: 0 0 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #f2f2f2;
    color: #2C3C64;

    font-size: 12px;
    font-weight: 700;
}

.purge-candidate-details {
    display: flex;
    flex-direction: column;

    min-width: 0;
}

.purge-candidate-details strong {
    color: #2C3C64;
    font-size: 14px;
}

.purge-email,
.purge-organisation {
    display: block;

    max-width: 220px;

    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;

    color: #777;
    font-size: 12px;
}

.purge-candidate-date {
    flex-shrink: 0;

    display: flex;
    flex-direction: column;

    text-align: right;
}

.purge-date {
    color: #C55359;
    font-size: 13px;
    font-weight: 600;
}

.purge-candidate-date small {
    margin-top: 2px;
    color: #999;
    font-size: 11px;
}

.purge-empty {
    flex: 1;

    min-height: 130px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;

    color: #888;
}

.purge-empty i {
    margin-bottom: 8px;

    color: #5cb85c;
    font-size: 30px;
}

.purge-empty strong {
    margin-bottom: 4px;

    color: #2C3C64;
    font-size: 14px;
}

.purge-empty span {
    max-width: 280px;

    font-size: 12px;
}

.purge-footer {
    margin-top: 12px;
    padding-top: 12px;

    border-top: 1px solid #eee;

    color: #777;
    font-size: 12px;
}

.purge-footer i {
    margin-right: 4px;
    color: #C55359;
}

@media (max-width: 767px) {

    .purge-candidate {
        align-items: flex-start;
        flex-direction: column;
    }

    .purge-candidate-date {
        padding-left: 46px;
        text-align: left;
    }

}

</style>
@endsection

@section('pageJavascript')
<script>
    // Update date and time dynamically
    function updateDateTime() {
        const now = new Date();
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = now.toLocaleDateString('en-GB', options);
        const time = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

        document.getElementById('current-date').textContent = date;
        document.getElementById('current-time').textContent = time;
    }

    updateDateTime();
    setInterval(updateDateTime, 10000); // Update every 10 seconds


    // Create the Pie Chart
    const ctx = document.getElementById('applicationStatusChart').getContext('2d');
    const data = [{{$pendingAndAwaiting}}, {{$incompleteUsers}}, {{$completedUsers}}];
    const labels = [
        "Haven't Started Yet",
        "In Progress",
        "Completed",
    ];
    const colors = ['#ff4d4f', '#ffa940', '#52c41a'];

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false, // Remove the legend
                }
            }
        }
    });


    document.addEventListener("DOMContentLoaded", function () {
        let durationData = JSON.parse(@json($averageDurations));

        if (Array.isArray(durationData) && durationData.length > 0) {
            const ctx = document.getElementById('applicationDurationChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: durationData.map(item => item.month), // Month Labels
                    datasets: [{
                        label: 'Avg Days to Complete',
                        data: durationData.map(item => item.avgDuration), // Average Duration
                        backgroundColor: 'rgba(44, 60, 100, 0.6)',
                        borderColor: 'rgba(44, 60, 100, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Average Days'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
        } else {
            console.log("No valid application duration data available.");
        }
    });
</script>
@endsection
