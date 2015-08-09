<style>
    .wbs-cta-buttons {
        clear: both;
        margin: 1em 0;
    }

    .wbs-cta-button {
        font-family: "Helvetica Neue", helvetica, sans-serif;
        background: #fff;
        padding: 10px 15px 10px 10px;
        font-size: 18px;
        font-weight: bold;
        color: #fff;
        display: inline-block;
        width: 310px;
        text-align: left;
        text-shadow: 1px 1px rgba(0, 0, 0, 0.85);
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        margin-bottom: 5px;
        margin-right: 2px;
        text-decoration: none;
    }

    .wbs-cta-button__icon {
        width: 35px;
        height: 35px;
        display: block;
        background: no-repeat top left url(data:image/png;base64,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);
        float: left;
        margin-right: 6px;
    }

    .wbs-cta-button__sub {
        font-size: 12px;
        font-weight: bold;
        display: block;
        color: #fff;
        text-shadow: 0 1px rgba(255, 255, 255, 0.65);
        font-style: normal;
        line-height: 1;
        margin-top: 0.5em;
    }

    .wbs-cta-button:hover, .wbs-cta-button:focus {
        color: #fff;
    }

    .wbs-cta-button:active {
        -webkit-box-shadow: inset 0 0 7px #333;
        -moz-box-shadow: inset 0 0 7px #333;
        box-shadow: inset 0 0 7px #333;
        color: #fff;
    }

    .wbs-cta-button--green {
        background: #98d463; /* Old browsers */
        background: -moz-linear-gradient(top, #98d463 0%, #66891d 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #98d463), color-stop(100%, #66891d)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #98d463 0%, #66891d 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #98d463 0%, #66891d 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #98d463 0%, #66891d 100%); /* IE10+ */
        background: linear-gradient(top, #98d463 0%, #66891d 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#98d463', endColorstr='#66891d', GradientType=0 ); /* IE6-9 */
    }

        .wbs-cta-button--green .wbs-cta-button__sub {
            color: #436c01;
        }

        .wbs-cta-button--green:hover, .wbs-cta-button--green:focus {
            background: #9dda67; /* Old browsers */
            background: -moz-linear-gradient(top, #9dda67 0%, #70971f 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #9dda67), color-stop(100%, #70971f)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, #9dda67 0%, #70971f 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, #9dda67 0%, #70971f 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, #9dda67 0%, #70971f 100%); /* IE10+ */
            background: linear-gradient(top, #9dda67 0%, #70971f 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#9dda67', endColorstr='#70971f', GradientType=0 ); /* IE6-9 */
        }

    .wbs-cta-button--blue {
        background: #2cbbfd; /* Old browsers */
        background: -moz-linear-gradient(top, #2cbbfd 0%, #006b9d 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #2cbbfd), color-stop(100%, #006b9d)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #2cbbfd 0%, #006b9d 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #2cbbfd 0%, #006b9d 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #2cbbfd 0%, #006b9d 100%); /* IE10+ */
        background: linear-gradient(top, #2cbbfd 0%, #006b9d 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2cbbfd', endColorstr='#006b9d', GradientType=0 ); /* IE6-9 */
    }

        .wbs-cta-button--blue .wbs-cta-button__sub {
            color: #065478;
        }

        .wbs-cta-button--blue:hover, .wbs-cta-button--blue:focus {
            background: #4cc6ff; /* Old browsers */
            background: -moz-linear-gradient(top, #4cc6ff 0%, #0078b0 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #4cc6ff), color-stop(100%, #0078b0)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, #4cc6ff 0%, #0078b0 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, #4cc6ff 0%, #0078b0 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, #4cc6ff 0%, #0078b0 100%); /* IE10+ */
            background: linear-gradient(top, #4cc6ff 0%, #0078b0 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4cc6ff', endColorstr='#0078b0', GradientType=0 ); /* IE6-9 */
        }

    .wbs-cta-button--orange {
        background: #f7a553; /* Old browsers */
        background: -moz-linear-gradient(top, #f7a553 0%, #c67524 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f7a553), color-stop(100%, #c67524)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #f7a553 0%, #c67524 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #f7a553 0%, #c67524 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #f7a553 0%, #c67524 100%); /* IE10+ */
        background: linear-gradient(top, #f7a553 0%, #c67524 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7a553', endColorstr='#c67524', GradientType=0 ); /* IE6-9 */
    }

        .wbs-cta-button--orange .wbs-cta-button__sub {
            color: #9c5918;
        }

        .wbs-cta-button--orange:hover, .wbs-cta-button--orange:focus {
            background: #fca14b; /* Old browsers */
            background: -moz-linear-gradient(top, #fca14b 0%, #c5650b 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fca14b), color-stop(100%, #c5650b)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top, #fca14b 0%, #c5650b 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top, #fca14b 0%, #c5650b 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top, #fca14b 0%, #c5650b 100%); /* IE10+ */
            background: linear-gradient(top, #fca14b 0%, #c5650b 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fca14b', endColorstr='#c5650b', GradientType=0 ); /* IE6-9 */
        }

    .wbs-cta-button__icon--adjust {
        background-position: 0 0;
    }

    .wbs-cta-button__icon--gift {
        background-position: 0 -35px;
    }

    .wbs-cta-button__icon--question {
        background-position: 0 -70px;
    }

    @media (max-width: 1240px) {
        .wbs-cta-button {
            width: 235px;
            text-align: center;
            line-height: 35px;
        }

        .wbs-cta-button__sub {
            display: none;
        }
    }
</style>

<div class="wbs-cta-buttons">

    <a
        class="wbs-cta-button wbs-cta-button--orange"
        href="https://wordpress.org/support/plugin/weight-based-shipping-for-woocommerce"
        target="_blank"
        title="Have a question? Welcome to support forum!"
    >
        <span class="wbs-cta-button__icon wbs-cta-button__icon--question"></span>
        Community Support
        <em class="wbs-cta-button__sub">Have a question? Welcome to support forum!</em>
    </a>

    <a
        class="wbs-cta-button wbs-cta-button--green"
        href="mailto:gravyzzap@gmail.com?subject=<?php echo esc_attr(rawurlencode("WBS support")) ?>"
        target="_blank"
        title="Direct email support. Paid and fast."
    >
        <span class="wbs-cta-button__icon wbs-cta-button__icon--gift"></span>
        Premium Support
        <em class="wbs-cta-button__sub">Direct email support. Paid and fast.</em>
    </a>

    <a
        class="wbs-cta-button wbs-cta-button--blue"
        href="mailto:gravyzzap@gmail.com?subject=<?php echo esc_attr(rawurlencode("WBS customization")) ?>"
        target="_blank"
        title="Need a modification? Drop us a line!"
    >
        <span class="wbs-cta-button__icon wbs-cta-button__icon--adjust"></span>
        Customization
        <em class="wbs-cta-button__sub">Need a modification? Drop us a line!</em>
    </a>
</div>