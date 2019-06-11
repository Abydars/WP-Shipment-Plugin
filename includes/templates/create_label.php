<div class="right-sidebar">
    <h1>Create Label</h1>
    <form method="POST" name="shipmentForm">
        <div class="basic-details">
            <div class="form-group">
                <label>Ticket ID</label>
                <input type="text" name="id">
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select name="customer">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Carrier</label>
                <select name="shipping_carrier">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Method</label>
                <select name="shipping_method">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Date</label>
                <input type="date" name="shipping_date" id="">
            </div>
        </div>
        <div class="packages">
            <h2>Packages</h2>
            <div class="package">
                <h4>Package #1</h4>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Weight</label>
                            <input type="text" name="weight">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Weight Unit</label>
                            <select name="unit">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Length</label>
                            <input type="text" name="length">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Width</label>
                            <input type="text" name="width">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>Height</label>
                        <input type="text" name="height">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="pickup">
                <div class="row">
                    <input type="checkbox" name="schedule" value="yes"> Schedule Pickup
                </div>
                <div class="pickup-schedule">
                    <div class="form-group">
                        <label>Pickup Date</label>
                        <input type="date" name="pickup_date">
                    </div>
                    <div class="row">
                        <div class="one-third">
                            <div class="form-group">
                                <label>Pickup Time</label>
                                <select name="pickup_time">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="one-fourth">
                            <div class="form-group">
                                <label></label>
                                <select name="">
                                    <option value="am">AM</option>
                                    <option value="pm">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>
    </form>
</div>