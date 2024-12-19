<!DOCTYPE html>
<html>
<head>
    <title>Informasi akun</title>
</head>
<body>
	<p>Informasi akun kamu </p>
    <table width="100%" border="0">
    <tr>
    	<td width="15%">Name</td>
        <td width="2%">:</td>
        <td>{{ $details['name'] }}</td>
    </tr>
    <tr>
    	<td>Email</td>
        <td>:</td>
        <td>{{ $details['email'] }}</td>
    </tr>
    <tr>
    	<td>Password</td>
        <td>:</td>
        <td>{{ $details['password'] }}</td>
    </tr>
    <tr>
    	<td>Created at</td>
        <td>:</td>
        <td>{{ date("d/m/Y H:i:s",strtotime($details['created_at'])) }}</td>
    </tr>
    </table>
</body>
</html>
