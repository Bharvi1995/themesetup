@component('mail::message')
    <p style="text-transform: capitalize;">Hi, Team </p>
    <p>Hope you are doing well, please find below <strong>{{ config('app.name') }}</strong> Jobs and failed jobs count.
    </p>
    <br />
    <h1 style="color: #B3ADAD !important">Jobs Count - {{ $jobsCount }}</h1>
    <h1 style="color: #B3ADAD !important">Failed Jobs Count - {{ $failedJobsCount }}</h1>
@endcomponent
