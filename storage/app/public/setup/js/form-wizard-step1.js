/*=========================================================================================
    File Name: form-wizard.js
    Description: wizard steps page specific js
==========================================================================================*/
$(function () {
    "use strict";

    var bsStepper = document.querySelectorAll(".bs-stepper"),
        select = $(".select2"),
        horizontalWizard = document.querySelector(".horizontal-wizard-example");

    // Adds crossed class
    if (typeof bsStepper !== undefined && bsStepper !== null) {
        for (var el = 0; el < bsStepper.length; ++el) {
            bsStepper[el].addEventListener("show.bs-stepper", function (event) {
                var index = event.detail.indexStep;
                var numberOfSteps = $(event.target).find(".step").length - 1;
                var line = $(event.target).find(".step");

                for (var i = 0; i < index; i++) {
                    line[i].classList.add("crossed");

                    for (var j = index; j < numberOfSteps; j++) {
                        line[j].classList.remove("crossed");
                    }
                }
                if (event.detail.to == 0) {
                    for (var k = index; k < numberOfSteps; k++) {
                        line[k].classList.remove("crossed");
                    }
                    line[0].classList.remove("crossed");
                }
            });
        }
    }

    // select2
    select.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
            placeholder: "Select value",
            dropdownParent: $this.parent(),
        });
    });

    // Horizontal Wizard
    // --------------------------------------------------------------------
    if (typeof horizontalWizard !== undefined && horizontalWizard !== null) {
        var numberedStepper = new Stepper(horizontalWizard),
            $form = $(horizontalWizard).find("form");
        $form.each(function () {
            var $this = $(this);
            $this.validate({
                ignore: "",
                rules: {
                    company_name: {
                        required: true,
                    },
                    company_registration_number: {
                        required: true,
                    },
                    company_registration_year: {
                        required: true,
                    },
                    company_address: {
                        required: true,
                    },
                    company_country_of_incorporation: {
                        required: true,
                    },
                    company_category: {
                        required: true,
                    },
                    doing_business_as: {
                        required: true,
                    },
                    company_email: {
                        required: true,
                        validate_email: true,
                    },
                    company_website: {
                        required: true,
                        validate_url: true,
                    },
                    domain_ownership: {
                        required: hasDO == "2" ? true : false,
                    },
                    article_of_incorporation: {
                        required: hasAOI == "2" ? true : false,
                    },
                },
            });
        });

        jQuery.validator.addMethod(
            "validate_email",
            function (value, element) {
                if (
                    /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(
                        value
                    )
                ) {
                    return true;
                } else {
                    return false;
                }
            },
            "Please enter a valid email."
        );

        jQuery.validator.addMethod(
            "validate_url",
            function (value) {
                return /^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(
                    value
                );
            },
            "Please enter a valid url."
        );

        $(horizontalWizard)
            .find(".btn-next")
            .each(function () {
                $(this).on("click", function (e) {
                    var isValid = $("form#form").valid();
                    if (isValid) {
                        $(this).parents("form").submit();
                    } else {
                        e.preventDefault();
                    }
                });
            });

        $(horizontalWizard)
            .find(".btn-prev")
            .on("click", function () {
                numberedStepper.previous();
            });

        $(horizontalWizard)
            .find(".btn-submit")
            .on("click", function () {
                var isValid = $(this).parent().siblings("form").valid();
                if (isValid) {
                    alert("Submitted..!!");
                }
            });
    }

    $("#company_registration_year")
        .select2()
        .change(function () {
            $(this).valid();
        });
    $("#company_country_of_incorporation")
        .select2()
        .change(function () {
            $(this).valid();
        });
    $("#company_category")
        .select2()
        .change(function () {
            $(this).valid();
        });
});