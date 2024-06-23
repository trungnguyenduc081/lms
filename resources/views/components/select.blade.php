@props(['options', 'value'])

<select {{ $attributes->merge(['class'=>'select rounded-md border-slate-700']) }}>
    @foreach($options as $val=>$option)
        <option @if(isset($value) && !is_array($value) && $value == $val) selected @endif @if(isset($value) && is_array($value) && in_array($val, $value)) selected @endif value="{{$val}}">{{$option}}</option>
    @endforeach
</select>