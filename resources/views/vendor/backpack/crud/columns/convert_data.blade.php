@php
    $value = data_get($entry, $column['name']);
    $value = str_replace('"','',$value);

    $data_file = App\Libs\DiskPathTools\DiskPathInfo::parse($value);
    $contents = json_decode($data_file->read());
@endphp

@foreach(@$contents as $key => $content)
    <p>{{$key}} : {{$content}}</p><br/>
@endforeach
