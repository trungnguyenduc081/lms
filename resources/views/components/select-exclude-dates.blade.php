@props(['options', 'value'])
@php
    $defaultOptions = [
        'every_sat'=>'Every Saturday',
        'every_sun'=>'Every Sunday',
    ];
    $options = array_merge($options, $defaultOptions);
@endphp

<select {{ $attributes->merge(['class'=>'select']) }}>
    @foreach($options as $val=>$option)
        <option @if(isset($value) && in_array($val, $value)) selected @endif value="{{$val}}">{{$option}}</option>
    @endforeach
</select>