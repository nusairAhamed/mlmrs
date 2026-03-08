<!DOCTYPE html>
<html>
<body>

<h2>Laboratory Report Ready</h2>

<p>Dear {{ $patient->full_name }},</p>

<p>Your laboratory report for order <strong>{{ $orderNumber }}</strong> is now ready.</p>

<p>You can view your report here:</p>

<p>
<a href="{{ $reportUrl }}">
View Report
</a>
</p>

<p>Or collect the report from the laboratory.</p>

<p>Thank you.</p>

</body>
</html>