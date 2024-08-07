<div class="card">
    <h3 class="card-header"><b>Technical Specification </b></h4>
        <hr>
        <div class="card-body">
            <div class="variants" id="specification">
                <div class="row specification-row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="parameterName"><b>Parameter Name</b></label>
                            <input type="text" name="parameterName[]" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="parameterRange"><b>Value</b></label>
                            <input type="text" name="parameterRange[]" class="form-control">
                        </div>
                    </div>
                    {{-- <div class="col-sm-3">
                        <div class="form-group">
                            <label for="parameterAccuracy"><b>Accuracy</b></label>
                            <input required type="text" name="parameterAccuracy[]" class="form-control">
                        </div>
                    </div> --}}
                    <div class="col-sm-3">
                        <div class="form-group">
                            <button class="btn btn-primary add-more" type="button" style="margin-top: 30px;">Add
                                More</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <span class="btn btn-link add-more" id="addMore"><i class="dripicons-plus"></i> @lang('file.Add New Attribute')</span> -->
        </div>
</div>