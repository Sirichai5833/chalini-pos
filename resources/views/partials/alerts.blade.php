@if(session('success'))
    <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
