@extends('layouts.basic')

@section('maincontent')
    <?php 
        if(isset($message))
        {
            echo $message;
        }
    ?>

@stop