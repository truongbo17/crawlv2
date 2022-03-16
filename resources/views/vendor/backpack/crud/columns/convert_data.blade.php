@php
    $value = data_get($entry, $column['name']);
    $value = str_replace('"','',$value);
     if($value === "null") return;

    $data_file = App\Libs\DiskPathTools\DiskPathInfo::parse($value);
    $contents = json_decode($data_file->read())
@endphp

@foreach(@$contents as $key => $content)
    @if(is_array($content))
        {{$key}} :
        @foreach($content as $contentChild)
            <p>{{$contentChild}}</p>
        @endforeach
    @else
        <p>{{$key}} : {{$content}}</p><br/>
    @endif
    <hr/>
@endforeach
