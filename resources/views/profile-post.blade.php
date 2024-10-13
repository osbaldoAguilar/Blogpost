<x-profile :sharedData="$sharedData" doctitle="{{ $sharedData['username'] }}'s Posts">
    <x-profile-list-group :posts="$posts" hideAuthor />
</x-profile>
