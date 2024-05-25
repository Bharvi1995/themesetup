<!DOCTYPE html>
<html>
<head>
<title>Application PDF</title>
<style type="text/css">
img {
    max-width: 100%;
}
body {
    -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em;
    background-color: #ffffff;
}
h1 {
    font-weight: 900 !important; margin: 20px 0px 15px 0px !important;
}
h2 {
    font-weight: 900 !important; margin: 20px 0 5px !important;
}
h1 {
    font-size: 22px !important;
}
h2 {
    font-size: 18px !important;
}
.container {
    margin: auto;
    margin-top: 70px;
}
.text-center {
    text-align: center;
}
table {
    border-spacing: 0;
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
}
.table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: left;
}
table tr .title {
    font-weight: 900;
    padding: 7px;
    background-color: #34383e;
    color: #ffffff;
}
p, h4, h3 {
    margin-top: 2px;
    margin-bottom: 2px;
}
h5 {
    margin-top: 2px;
    margin-bottom: 2px;
    color: #34383e;
    font-size: 11px;
}
</style>
</head>
<body style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #ffffff; margin: 0;" bgcolor="#ffffff">
    <div class="container">
        <h1 class="text-center">Merchant Account Application</h1>
        <div class="application-table">
            <table class="table table-bordered">
                <tr>
                   <td colspan="2" class="title">Application Details</td>
                </tr>
                <tr>
                    <th>Id</th>
                    <td>{{ $data->id }}</td>
                </tr>
                <tr>
                    <th>User Name</th>
                    <td>{{ $data->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $data->email }}</td>
                </tr>    
                <tr>
                    <th>Business Category</th>
                    <td>{{ $data->business_type }}</td>
                </tr>
                <tr>
                    <th>Accepted Payment Methods</th>
                    <td>
                        @if($data->accept_card != null )
                        @foreach (json_decode($data->accept_card) as $item )
                        <span class='badge badge-sm badge-success'>{{ $item }}</span>
                        @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <td>{{ $data->business_name }}</td>
                </tr>
                <tr>
                    <th>Website URL</th>
                    <td>{{ $data->website_url }}</td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td>{{ $data->business_contact_first_name }}</td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>{{ $data->business_contact_last_name }}</td>
                </tr>
                <tr>
                    <th>Company Address</th>
                    <td>{{ $data->business_address1 }}</td>
                </tr>
                <tr>
                    <th>Residential Address</th>
                    <td>{{ $data->residential_address }}</td>
                </tr>
                <tr>
                    <th>Country Of Incorporation</th>
                    <td>{{ $data->country }}</td>
                </tr>
                <tr>
                    <th>Phone Number </th>
                    <td>+{{ $data->country_code }} {{ $data->phone_no }}</td>
                </tr>
                <tr>
                    <th>Contact Details</th>
                    <td>{{ $data->skype_id }}</td>
                </tr>
                <tr>
                    <th>Processing Currency</th>
                    <td>
                        @if($data->processing_currency != null)
                            <?php
                            $a = json_decode($data->processing_currency);
                            foreach ($a as $key => $value) {
                                echo "<span class='badge badge-sm badge-success'>".$value."</span> ";
                            }
                        ?>
                            @endif
                    </td>
                </tr>
                <tr>
                    <th>Integration Preference </th>
                    <td>
                        @if($data->technology_partner_id != null)
                            <?php
                            $a = json_decode($data->technology_partner_id);
                            foreach ($a as $key => $value) {
                                echo "<span class='badge badge-sm badge-success'>".getTechnologyPartnerName($value)."</span> ";
                            }
                        ?>
                            @endif
                    </td>
                </tr>

                <tr>
                    <th>Processing Country</th>
                    <td>
                    @if($data->processing_country != null)
                    <?php
                        $a = json_decode($data->processing_country);
                        foreach ($a as $key => $value) {
                            if($value != 'Others'){
                                echo "<span class='badge badge-sm badge-success'>".$value."</span> ";
                            }
                        }
                    ?>
                    @endif
                    @if($data->other_processing_country != null)
                    <span class="badge badge-primary badge-sm">{{ $data->other_processing_country }}</span>
                    @endif
                    </td>
                </tr>

                <tr>
                    <th>Industry Type</th>
                    <td>
                    @if(isset($data->category_id))
                    @if(getCategoryName($data->category_id) != 'Miscellaneous')
                    <span class='badge badge-sm badge-success'>{{ getCategoryName($data->category_id) }}</span>
                    @else
                    @if($data->other_industry_type != null)
                    <span class="badge badge-primary badge-sm">{{ $data->other_industry_type }}</span>
                    @endif
                    @endif
                    @else
                    ---
                    @endif
                    </td>
                </tr>

                                
                <tr>
                    <th>Licence Status</th>
                    <td>
                        @if($data->company_license == 0)
                        Licenced
                        @elseif($data->company_license == 1)
                        Unlicenced
                        @elseif($data->company_license == 2)
                        NA
                        @else
                        ---
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Monthly Volume</th>
                    <td>
                        {{ $data->monthly_volume_currency }} {{ $data->monthly_volume }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>