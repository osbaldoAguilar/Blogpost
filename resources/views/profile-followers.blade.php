<x-profile :sharedData="$sharedData" doctitle="{{ $sharedData['username'] }}'s Followers">
    <div class="list-group">
        @foreach ($followers as $follower)
            <a href="/profile/{{ $follower->userThatFollows->username }}" class="list-group-item list-group-item-action">
                <img class="avatar-tiny" src="{{ $follower->userThatFollows->avatar }}" />
                {{ $follower->userThatFollows->username }}
                {{-- <strong>{{ $follower->user_id }}</strong> on {{ $follower->created_at->format('n/j/Y') }} --}}
            </a>
        @endforeach
    </div>
</x-profile>
