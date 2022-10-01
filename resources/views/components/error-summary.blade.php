@if($errors->hasBag($bagName ?? 'default'))
    <div class="rounded bg-red-200 border border-1 border-red-500 p-3 my-4">
        @isset($summaryMessage)
            <p class="mb-3">{{$summaryMessage}}</p>
        @endisset
        <ul class="list-disc ml-5">
            @foreach($errors->{$bagName ?? 'default'}->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
        @isset($postSummaryMessage)
            <p class="mt-5">{{$postSummaryMessage}}</p>
        @endisset
    </div>
@endif
