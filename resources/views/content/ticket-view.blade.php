@extends('layouts.contentLayoutMaster')

@section('title', 'Ticket details')

@section('vendor-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection

@section('page-style')
    {{-- Page Css files --}}
    <link rel="stylesheet" href="{{ asset('css/base/pages/page-blog.css') }}"/>
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
    <link rel="stylesheet" href="{{asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css'))}}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
@endsection
@section('content')
    <!-- users list start -->
    <div class="blog-detail-wrapper">
        <div class="row">
            <!-- Blog -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{$ticket->subject}}</h4>
                        <div class="d-flex">
                            <i style="width: 24px;height: 24px" data-feather="user"></i>
                            <div class="author-info">
                                <small class="text-muted me-25">by</small>
                                <small>
                                    <a href="#" class="text-body">{{$ticket->user->contact_name}}
                                        @if($ticket->user->userable instanceof \App\Models\Owner)
                                            (Owner)
                                        @elseif($ticket->user->userable instanceof \App\Models\Tenant)
                                            (Charterer)
                                        @elseif($ticket->user->userable instanceof \App\Models\Office)
                                            (Office)
                                        @endif

                                    </a>
                                </small>
                                <span class="text-muted ms-50 me-25">|</span>
                                <small class="text-muted">{{$ticket->created_at}}</small>
                            </div>
                        </div>
                        <div class="my-1 py-25">
                            @php
                                $status = [
                                    1=> ['title'=> 'New', 'class'=> 'badge-light-warning status-switcher'],
                                    2=> ['title'=> 'Open', 'class'=> 'badge-light-info status-switcher'],
                                    3=> ['title'=> 'Closed', 'class'=> 'badge-light-secondary status-switcher']
                                ];

                            $category = [
                                    1=> ['title'=> 'Payment'],
                                    2=> ['title'=> 'Shipment'],
                                    3=> ['title'=> 'Other']
                                ];
                            @endphp

                            <a href="#">
                                <span class="badge rounded-pill {{$status[$ticket->status]['class']}} me-50">
                                    {{$status[$ticket->status]['title']}}
                                </span>
                            </a>
                            <a href="#">
                                <span class="badge rounded-pill badge-light-primary me-50">
                                    {{$category[$ticket->type]['title']}}
                                </span>
                            </a>
                        </div>
                        <p class="card-text mb-2">{!! $ticket->description !!}</p>
                        <hr class="my-2"/>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                @if($ticket->admin)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <i style="width: 32px;height: 32px" data-feather="user"></i>
                                        </div>
                                        <div class="author-info">
                                            <h6 class="fw-bolder">{{$ticket->admin->name}}
                                                (#{{$ticket->admin->dealgo_id}})</h6>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center">
                                        <div class="author-info">
                                            <h6 class="fw-bolder">Not assigned yet</h6>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($ticket->status !== 3)
                                <div class="dropdown blog-detail-share">
                                    <i data-feather="power" data-id="{{$ticket->id}}"
                                       class="font-medium-5 text-body status-switcher"
                                       role="button"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Ticket -->

        @if ($ticket->replies->count() > 0)
            <!-- Ticket Replies -->
                <div class="col-12 mt-1" id="blogComment">
                    <h6 class="section-label mt-25">All replies</h6>
                    @foreach($ticket->replies as $reply)
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="me-2 @if($reply->author instanceof \App\Models\Admin) avatar @endif">
                                        <i style="width: 32px;height: 32px" data-feather="user"></i>
                                    </div>
                                    <div class="author-info">
                                        @if($reply->author instanceof \App\Models\Admin)
                                            <h6 class="fw-bolder mb-25">{{$reply->author->name}}
                                                (#{{$reply->author->dealgo_id}})</h6>
                                        @else
                                            <h6 class="fw-bolder mb-25">{{$reply->author->contact_name}}
                                                @if($reply->author->userable instanceof \App\Models\Owner)
                                                    (Owner)
                                                @elseif($reply->author->userable instanceof \App\Models\Tenant)
                                                    (Charterer)
                                                @elseif($reply->author->userable instanceof \App\Models\Office)
                                                    (Office)
                                                @endif
                                            </h6>
                                        @endif
                                        <p class="card-text">{{$reply->created_at}}</p>
                                        <p class="card-text">
                                            @php
                                                $quill = new \DBlackborough\Quill\Render($reply->text, 'HTML');
                                            @endphp
                                            {!! $quill->render() !!}
                                        </p>
                                        @if($ticket->status !== 3)
                                            <a href="#reply-form">
                                                <div class="d-inline-flex align-items-center">
                                                    <i data-feather="corner-up-left" class="font-medium-3 me-50"></i>
                                                    <span>Reply</span>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!--/  Ticket Replies  -->
        @endif
        <!-- Leave a reply -->
            @if($ticket->status === 1 || ($ticket->admin->id === auth('admins')->user()->id && $ticket->status === 2 ))
                <div class="col-12 mt-1">
                    <h6 class="section-label mt-25">Reply</h6>
                    <div class="card">
                        <form action="{{route('admin.reply',[$ticket->id])}}" id="reply-form" class="form add-new-reply"
                              method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div style="min-height: 250px;height: 250px">
                                            <div class="editor"></div>
                                            <textarea name="reply" style="display:none" id="hiddenReply"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        @endif
        <!--/ Leave a Blog Comment -->
        </div>
    </div>
    <!-- users list ends -->
@endsection

@section('vendor-script')
    {{-- Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/pages/ticket-view.js')) }}"></script>
@endsection
