<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flasher/flasher@2.2.0/dist/flasher.min.css">

@if ($errors->any())
<script type="text/javascript">
    @foreach($errors->all() as $error)
        @php
            $string = Str::of($error);
            $required = ' wajib diisi';
            if($string->contains($required)){
                $error = formatAttribute($string->before($required)).$required ?? $error;
            }
        @endphp
        flasher.error('{{ $error }}');
    @endforeach
</script>
@endif

@if(session()->has('success') && !request()->ajax())
<script type="text/javascript">
    flasher.error('{{ $error }}');
</script>
@php
session()->forget('success');
@endphp
@endif

@if(session()->has('error') && !request()->ajax())
<script type="text/javascript">
       flasher.error('{{ $error }}');
</script>
@php
session()->forget('error');
@endphp
@endif