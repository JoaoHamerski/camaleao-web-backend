@foreach(glob(public_path() . '/css/pdf/*') as $path)
<link rel="stylesheet" href="{{ $path }}">
@endforeach
