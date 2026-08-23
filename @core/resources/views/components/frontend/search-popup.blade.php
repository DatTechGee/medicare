<div class="search navbar-search position-relative">
    <div class="search-open">
        <i class="las la-search icon"></i>
    </div>
    <div class="search-bar">
        <form class="menu-search-form" action="{{ route('frontend.donation.search') }}">
            <div class="search-close"> <i class="las la-times"></i> </div>
            <input class="item-search" name="search" id="search" type="text" placeholder="{{__('Search Here.....')}}">
            <button type="submit">{{__('Search')}}</button>
        </form>
    </div>
</div>