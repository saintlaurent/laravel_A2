{{Form::open()}} 
        <div>
            {{Form::label('email', 'Email (will be your username): ')}}
            {{Form::text('email')}} 
            
            <!-- print error message -->
            @if($errors->has('email'))
                {{$errors->first('email')}}
            @endif
        </div>
    
        <div>
            {{Form::submit('Submit')}}
        </div>
    {{Form::close()}}