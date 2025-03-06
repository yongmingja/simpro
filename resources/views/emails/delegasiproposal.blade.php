<!DOCTYPE html>
<html>
<head>
    <title>Surat Pemberitahuan Delegasi</title>
</head>
<body>
    <h3>{{ $data['name'] }}</h3>
    <h4>{!! $data['body'] !!}</h4>
    <h4>Anda bisa lihat detail proposal pada link berikut {{ $data['link'] }}.</h4>

    <br>
    <br>
    <h5>Regards,</h5>
    <h5><i>Staf UPT SI</i></h5>
</body>
</html>