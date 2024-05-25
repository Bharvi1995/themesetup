<div class="table-responsive custom-table">
    <table class="table table-borderless table-striped">
        <tbody>
            <tr>
                <td>
                    <strong>First Name</strong>
                    <p class="mb-0">{{ $data->business_contact_first_name }}</p>
                </td>
                <td>
                    <strong>Last Name</strong>
                    <p class="mb-0">{{ $data->business_contact_last_name }}</p>
                </td>
                <td>
                    <strong>Email</strong>
                    <p class="mb-0">{{ $data->email }}</p>
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Company Name</strong>
                    <p class="mb-0">{{ $data->business_name }}</p>
                </td>
                <td>
                    <strong>Industry Type</strong>
                    <br>
                    @php
                    $categoryName = getCategoryName($data->category_id);
                    @endphp
                    @if(isset($data->category_id))
                    @if($categoryName != 'Miscellaneous')
                    <span class='badge badge-sm badge-primary'>{{ $categoryName }}</span>
                    @else
                    @if($data->other_industry_type != null)
                    <span class="badge badge-primary badge-sm">{{ $data->other_industry_type }}</span>
                    @endif
                    @endif
                    @else
                    ---
                    @endif
                </td>
                <td>
                    <strong>Your Website URL</strong>
                    <p class="mb-0">{{ $data->website_url }}</p>
                </td>
            </tr>

            <tr>
                
                <td>
                    <strong>Phone Number</strong>
                    <p class="mb-0">+{{ $data->country_code }} {{ $data->phone_no }}</p>
                </td>
                <td >
                    <strong>Contact Details</strong>
                    <p class="mb-0">{{ $data->skype_id }}</p>
                </td>
                <td>
                    <strong>License Status</strong>
                    <p class="mb-0">
                    @if($data->company_license == 0)
                    Licensed
                    @elseif($data->company_license == 1)
                    Unlicensed
                    @elseif($data->company_license == 2)
                    NA
                    @else
                    ---
                    @endif
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Registeration number</strong>
                    <p class="mb-0">{{ $data->business_category }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>