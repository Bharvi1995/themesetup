$("#application-form").submit(function () {
    $(this)
        .find("input:text")
        .each(function () {
            $(this).val($.trim($(this).val()));
        });
});

$(document).ready(function () {
    if (!isEditPage) {
        var value = $(".boardOfDirectors").val();
        if (value > 1) {
            dynamicApplicationFields(value);
        } else {
            $(".dynamicPassportFields").empty();
            $(".dynamicUtilityBillFields").empty();
            $(".dynamicBankStatementFields").empty();
        }
    }
});

$(document).on("focusout", ".boardOfDirectors", function () {
    $(".BODClientSide").text("");
    var value = $(this).val();
    var oldValue = $(".boardOfDirectorVal").val();
    if (value <= 10) {
        if (isEditPage) {
            if (value > oldValue) {
                var newVal = value - oldValue;
                if (newVal > 1) {
                    dynamicApplicationFields(newVal);
                } else {
                    $(".dynamicPassportFields").empty();
                    $(".dynamicUtilityBillFields").empty();
                    $(".dynamicBankStatementFields").empty();
                }
            } else {
                $(".dynamicPassportFields").empty();
                $(".dynamicUtilityBillFields").empty();
                $(".dynamicBankStatementFields").empty();
            }
        } else {
            if (value > 1) {
                dynamicApplicationFields(value);
            } else {
                $(".dynamicPassportFields").empty();
                $(".dynamicUtilityBillFields").empty();
                $(".dynamicBankStatementFields").empty();
            }
        }
    } else {
        $(".BODClientSide").text("The maximum value should be 10.");
    }
});

function dynamicApplicationFields(value) {
    $(".dynamicPassportFields").empty();
    $(".dynamicUtilityBillFields").empty();
    $(".dynamicBankStatementFields").empty();
    for (var i = 1; i < value; i++) {
        $(".dynamicPassportFields").append(addPassportFields(i));
        $(".dynamicUtilityBillFields").append(addUtilityBillField(i));
        $(".dynamicBankStatementFields").append(addBankStatementField(i));
    }
}

function addPassportFields(item) {
    var html = '<div class="mt-3 form-group">';
    html += `<label>Passport ${
        item + 1
    } <span class="text-danger">*</span></label>`;
    html += '<div class="custom-file">';
    html += `<input type="file" class="form-control passportFile" id="passportInput${item}" name="passport[]">`;
    html += '<span class="text-danger help-block form-error"></span>';
    html += "</div>";
    html += "</div>";
    return html;
}

function addUtilityBillField(item) {
    var html = '<div class="mt-3">';
    html += `<label>Utility Bill ${
        item + 1
    } <span class="text-danger">*</span></label>`;
    html += '<div class="custom-file">';
    html += `<input type="file" class="form-control utilityFile"  name="utility_bill[]">`;
    html += '<span class="text-danger help-block form-error"></span>';
    html += "</div>";
    html += "</div>";

    return html;
}

function addBankStatementField(item) {
    var html = '<div class="mt-3">';
    html += `<label>Company's Bank Statement (last 180 days) ${
        item + 1
    } <span class="text-danger">*</span></label>`;
    html += '<div class="custom-file">';
    html += `<input type="file" class="form-control bankStatementFile" name="latest_bank_account_statement[]">`;
    html += '<span class="text-danger help-block form-error"></span>';
    html += "</div>";
    html += "</div>";

    return html;
}
