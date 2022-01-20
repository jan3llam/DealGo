<!-- Edit User Modal -->
<div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-edit-user">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-5 px-sm-5 pt-50">
                <div class="text-center mb-2">
                    <h1 class="mb-1">تعديل بيانات المركز الطبي</h1>
                </div>
                <form id="editUserForm" class="row gy-1 pt-75" method="POST" enctype="multipart/form-data"
                      action="{{route('admin.provider.update')}}">
                    @csrf
                    <input type="hidden" id="object-id" name="object_id" value="{{$object->id}}">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="name">الاسم</label>
                        <input type="text" id="name" name="name" class="form-control"
                               placeholder="الاسم" value="{{$object->name}}"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="description">الوصف</label>
                        <input type="text" id="description" name="description" class="form-control"
                               value="{{$object->description}}" placeholder="الوصف"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="address">العنوان</label>
                        <input type="text" id="address" name="address" class="form-control"
                               value="{{$object->address}}" placeholder="العنوان"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="phone">رقم الموبايل</label>
                        <input
                            type="text" id="phone" name="phone" class="form-control modal-edit-tax-id"
                            placeholder="رقم الموبايل" value="{{$object->phone}}"/>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="specification">التخصص</label>
                        <select class="form-control dt-full-name" id="specification" name="specification">
                            @foreach($specifications as $specification)
                                <option value="{{$specification->id}}">{{$specification->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="city">المدينة</label>
                        <select class="form-control dt-full-name" id="city" name="city">
                            @foreach($cities as $city)
                                <option value="{{$city->id}}">{{$city->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <input type="hidden" id="longitude" name="longitude" value="{{$object->longitude}}">
                        <input type="hidden" id="latitude" name="latitude" value="{{$object->latitude}}">
                        <label class="form-label" for="region">الموقع على الخريطة</label>
                        <div id="map" style="min-height: 200px"></div>
                    </div>
                    <div class='col-12 col-md-6'>
                        <label for="file" class="form-label">الصورة</label>
                        <input class="form-control" type="file" name="image" id="file"/>
                        <p class="help-block">W 873 x H 355</p>
                    </div>
                    <div class="col-12 text-center mt-2 pt-50">
                        <button type="submit" class="btn btn-primary me-1">إرسال</button>
                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                aria-label="Close">
                            تراجع
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--/ Edit User Modal -->
