<x-profile :sharedData="$sharedData" doctitle="{{ $sharedData['username'] }}'s Posts">
    @include('profile-only', ['posts' => $posts]);
    {{-- <x-profile-list-group :posts="$posts" hideAuthor /> --}}
</x-profile>
