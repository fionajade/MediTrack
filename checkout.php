<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout</title>
<script src="https://www.paypal.com/sdk/js?client-id=AcS-hjIRvQLZKuVCNSJC537dSiUWfK279xTZ_h28WbGv0sT9STVm6xg4AsYn_2lMwRSD6nQGW--iiQdd&currency=PHP"></script>
</head>
<body>

<h2>Pay with PayPal</h2>
<div id="paypal-button-container"></div>

<script>
const cart = JSON.parse(localStorage.getItem("cart"));

paypal.Buttons({
    createOrder() {
        return fetch("paypal-create-order.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify({cart})
        }).then(res => res.json()).then(data => data.id);
    },

    onApprove(data) {
        return fetch("paypal-capture-order.php?orderID=" + data.orderID, {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify({cart})
        }).then(res => res.json()).then(result => {
            if (result.success) {
                alert("Payment successful");
                localStorage.removeItem("cart");
                location.href = "receipt.php";
            } else alert(result.error);
        });
    }
}).render('#paypal-button-container');
</script>

</body>
</html>
