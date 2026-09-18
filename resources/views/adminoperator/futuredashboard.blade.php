@extends('layout.adminoperator')

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
        <!-- Placeholder Columns -->
        <div class="card placeholder-card">
            <p>Coming soon</p>
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
