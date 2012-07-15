_jq.extend(x5engine.imCart, {

	_restoreSpecialChars: function (str) {
		return str.replace(/\{1\}/g, "'").replace(/\{2\}/g, "\"").replace(/\{3\}/g, "\\").replace(/\{4\}/g, "<").replace(/\{5\}/g, ">")
	},
	
	// Test if cookies are working in the current browser
	_testCookie: function () {
		_jq.imCookie("imCookieTest", "test_content");
		if (_jq.imCookie("imCookieTest") == "test_content") return true;
		return false;
	},

	// Get the product array from cookie
	_getCart: function () {
		try {
			if (_jq.imCookie(x5engine.imCart.costants.COOKIE_NAME, null, { path: '/' }) != null)
				return _jq.parseJSON(_jq.imCookie(x5engine.imCart.costants.COOKIE_NAME, null, { path: '/' }));
			else
				return [];
		} catch (e) {
			_jq.imCookie(x5engine.imCart.costants.COOKIE_NAME, {}, { path: '/' });
			return [];
		}
	},

	// Save the product array to the cookie
	_setCart: function (products) {
		if (!x5engine.imCart._testCookie()) {
			alert(x5engine.l10n.getLocalization("cart_err_cookie"));
			return false;
		}

		var json = [];
		for (var i = 0, j = 0; i < products.length; i++)
		if (products[i] != null) {
			json[j] = "{\"id\": \"" + products[i].id + "\", \"quantity\": " + products[i].quantity + ", \"option\": \"" + products[i].option + "\"}";
			j++;
		}
		json = json.join(",");
		json = "[" + json + "]";

		_jq.imCookie(x5engine.imCart.costants.COOKIE_NAME, json, { path: '/' });
	},

	// Save the form data in a cookie
	_setFormData: function (post) {
		var json;
		for (var i = 0; i < post.length; i++) {
			post[i] = "{\"id\": \"" + post[i].id + "\", \"val\": \"" + post[i].val + "\"}";
		}
		json = "[" + post.join(",") + "]";
		_jq.imCookie(x5engine.imCart.costants.COOKIE_FORM_NAME, json);
	},

	// Get the form data from the cookie
	_getFormData: function () {
		try {
			return _jq.parseJSON(_jq.imCookie(x5engine.imCart.costants.COOKIE_FORM_NAME));
		} catch (e) {
			_jq.imCookie(x5engine.imCart.costants.COOKIE_FORM_NAME, {});
			return null;
		}
	},

	// Set the currency format
	_setCurrency: function (n, format, currency) {
		var decsep = "";
		var thsep = "";
		var integer;
		var decimal = "";
		currency = currency || x5engine.imCart.settings.currency;
		if (format == null) format = x5engine.imCart.settings.currency_format;
		var value = "";

		// Find the decimal separator
		decsep = Math.max(format.lastIndexOf("."), format.lastIndexOf(","));
		thsep = Math.min(format.indexOf("."), format.indexOf(","));
		if (thsep == -1 && format.indexOf(".") != -1) thsep = format.indexOf(".");
		if (thsep == -1 && format.indexOf(",") != -1) thsep = format.indexOf(",");

		if (decsep != -1) {
			integer = format.substr(0, decsep);
			decimal = format.substr(decsep + 1);
		} else
			integer = format;
			
		// Set the precision
		var counter = 0;
		for (var i = 0; i < decimal.length; i++)
			if (decimal.charAt(i) == '@') 
				counter++;
			
		var value = new Number(n);
		value = value.toFixed(counter);
		var int_value = Math.floor(value);
		var dec_value = value.toString();
		dec_value = dec_value.substring(dec_value.length - counter);

		if (decsep != thsep && thsep != -1) thsep = format.charAt(thsep);
		else
		thsep = "";
		if (decsep == -1) decsep = "";
		else decsep = format.charAt(decsep);

		// Format the int part
		int_value += "";
		for (var i = 0; i < Math.max(integer.length, int_value.length); i++) {
			if (i % 3 == 0 && i != 0 && (int_value.length - i > 0 || integer.charAt(integer.length - 2 - i) == "@")) value = thsep + value;
			if (int_value.length - 1 - i >= 0) value = int_value.charAt(int_value.length - 1 - i) + value;
			else if (integer.charAt(integer.length - 1 - i) == "@") value = "0" + value;
		}

		if (counter == 0 && dec_value > 0) {
			counter = 2;
			decimal = "@@";
		}			
				
		// Add the decimal part
		value = int_value + decsep.toString() + dec_value;
		decimal = decimal.replace("[C]", "");

		// Add the currency symbol
		if (format.indexOf("[C]") == 0) value = currency + value;
		else if (format.indexOf("[C]") != -1) value += currency;

		return value;
	},

	// Get the value of the key
	_getValueFromKey: function (key, array) {
		var value;
		if (array["0"] == null) 
			value = 0;
		else
			value = array["0"];
			
		if (key * 1 > 0) 
			for (var property in array)
				if (property * 1 <= key * 1) 
					value = array[property];

		return value;
	},

	// Get the shipping price
	_getShippingPrice: function (shipping, subtotal, total_quantity, total_weight) {
		if (shipping.price != null) {
			switch (shipping.type) {
				case "WEIGHT":
					price = x5engine.imCart._getValueFromKey(total_weight, shipping.price);
					break;
				case "QUANTITY":
					price = x5engine.imCart._getValueFromKey(total_quantity, shipping.price);
					break;
				case "AMOUNT":
					price = x5engine.imCart._getValueFromKey(subtotal, shipping.price);
					break;
				default:
					price = shipping.price;
					break;
			}
			if (shipping.vat != null) 
				price += price * shipping.vat;
			return price;
		}

		return 0;
	},

	// Set the payment type
	_setPayment: function (n) {
		x5engine.imCart.payment_type = n;
	},

	// Get the payment type
	_getPayment: function () {
		return x5engine.imCart.payment_type;
	},

	// Set the shipping type
	_setShipping: function (n) {
		x5engine.imCart.shipping_type = n;
	},

	// Get the shipping type
	_getShipping: function () {
		return x5engine.imCart.shipping_type;
	},

	// Empty the cookie and reset the cart
	_realEmptyCart: function () {
		x5engine.imCart._setCart([]);
		x5engine.imCart.updateWidget();
		if (_jq("#imInputTotalPrice").length > 0) x5engine.imCart.showCart();
	},

	// Create a string ready to be used in an HTML/JS string
	_htmlOutput: function (str) {
		return str.replace(/\"/g, "''").replace(/\</g, "&lt;").replace(/\>/g, "&gt;");
	},
	
	// Create the order number
	_createOrderNo: function (format) {
		if (format == null) format = x5engine.imCart.settings.order_no_format;
		var date = new Date();
		var day = date.getDate();
		var month = date.getMonth() + 1;
		var shortYear = date.getYear().toString();
		shortYear = shortYear.substring(shortYear.length - 2);
		if (parseInt(day) < 10) day = "0" + day;
		if (parseInt(month) < 10) month = "0" + month;

		format = format.replace(/\[dd\]/g, day);
		format = format.replace(/\[mm\]/g, month);
		format = format.replace(/\[yy\]/g, shortYear);
		format = format.replace(/\[yyyy\]/g, date.getFullYear());
		while (format.match(/\[A-Z\]/)) format = format.replace(/\[A-Z\]/, String.fromCharCode(Math.round(Math.random() * 25 + 65)));
		while (format.match(/\[a-z\]/)) format = format.replace(/\[a-z\]/, String.fromCharCode(Math.round(Math.random() * 25 + 97)));
		while (format.match(/\[0-9\]/)) format = format.replace(/\[0-9\]/g, Math.round(Math.random() * 9));

		return format;
	},
	
	// Create the correct HTML code for operational purposes
	_createPaymentHtml: function (paymentHtml) {
	
		// Replaces the keywords
		if (x5engine.imCart.email_data.order_no == null || x5engine.imCart.email_data.order_no == "")
			x5engine.imCart.email_data.order_no = x5engine.imCart._createOrderNo();
		paymentHtml = paymentHtml.replace(/\[ORDER_NO\]/g, x5engine.imCart.email_data.order_no);
		paymentHtml = paymentHtml.replace(/\[QUANTITY\]/g, x5engine.imCart.email_data.total_quantity);
		paymentHtml = paymentHtml.replace(/\[WEIGHT\]/g, x5engine.imCart.email_data.total_weight);
		var price = x5engine.imCart.email_data.clear_total;
		// Format the price
		var format = x5engine.imCart.settings.cart_price;
		if (format.multiplier != null) price *= format.multiplier;
		price = x5engine.imCart._setCurrency(price.toString(), format.format);
		paymentHtml = paymentHtml.replace(/\[PRICE\]/g, price);
		while (paymentHtml.match(/\[PRICE,\s*([0-9]+),\s*([\w#@\.,\[\]]+)\]/)) {
			price = x5engine.imCart.email_data.clear_total;
			price *= parseInt(RegExp.$1);
			price = x5engine.imCart._setCurrency(price.toString(), RegExp.$2);
			paymentHtml = paymentHtml.replace(/\[PRICE,\s*([0-9]+),\s*([\w#@\.,\[\]]+)\]/, price);
		}
		
		//Customer data fields
		post = x5engine.imCart.email_data.form;
		if (post != null && post["imCartName_shipping"] != null && post["imCartName_shipping"] != "") {
			paymentHtml = paymentHtml.replace(/\[FIRST_NAME\]/g, ((post["imCartName_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartName_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[LAST_NAME\]/g, ((post["imCartLastName_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartLastName_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ADDRESS1\]/g, ((post["imCartAddress1_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartAddress1_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ADDRESS2\]/g, ((post["imCartAddress2_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartAddress2_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[CITY\]/g, ((post["imCartCity_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartCity_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[STATE\]/g, ((post["imCartStateRegion_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartStateRegion_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[COUNTRY\]/g, ((post["imCartCountry_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartCountry_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ZIP\]/g, ((post["imCartZipPostalCode_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartZipPostalCode_shipping"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[EMAIL\]/g, ((post["imCartEmail_shipping"] != null) ? x5engine.imCart._htmlOutput(post["imCartEmail_shipping"].imValue) : ""));
		} else if (post != null) {
			paymentHtml = paymentHtml.replace(/\[FIRST_NAME\]/g, ((post["imCartName"] != null) ? x5engine.imCart._htmlOutput(post["imCartName"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[LAST_NAME\]/g, ((post["imCartLastName"] != null) ? x5engine.imCart._htmlOutput(post["imCartLastName"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ADDRESS1\]/g, ((post["imCartAddress1"] != null) ? x5engine.imCart._htmlOutput(post["imCartAddress1"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ADDRESS2\]/g, ((post["imCartAddress2"] != null) ? x5engine.imCart._htmlOutput(post["imCartAddress2"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[CITY\]/g, ((post["imCartCity"] != null) ? x5engine.imCart._htmlOutput(post["imCartCity"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[STATE\]/g, ((post["imCartStateRegion"] != null) ? x5engine.imCart._htmlOutput(post["imCartStateRegion"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[COUNTRY\]/g, ((post["imCartCountry"] != null) ? x5engine.imCart._htmlOutput(post["imCartCountry"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[ZIP\]/g, ((post["imCartZipPostalCode"] != null) ? x5engine.imCart._htmlOutput(post["imCartZipPostalCode"].imValue) : ""));
			paymentHtml = paymentHtml.replace(/\[EMAIL\]/g, ((post["imCartEmail"] != null) ? x5engine.imCart._htmlOutput(post["imCartEmail"].imValue) : ""));
		}

		return paymentHtml;
	},

	_updateOption: function (id, old_option, new_option, quantity, obj) {
		x5engine.imCart.addProduct(id, 0, old_option, true, false);
		x5engine.imCart.addProduct(id, quantity, new_option);
		x5engine.imCart.showCart(_jq(obj).attr("id"), 1);
	},
	
	cartPage: function () {
		var category = x5engine.utils.getParam("category");
		if (category != null && category != "")
			x5engine.imCart.showCategory(x5engine.utils.getParam("category"));
		else
			x5engine.imCart.showCart();
	},
	
	// Save the current page and go to the cart
	gotoCart: function (in_cart) {
		if (typeof in_cart == "string") {
			_jq.imCookie("imShopPage", window.location.href, { path: '/' });
			location.href = in_cart + "cart/index.html";
			return true;
		} else {
			if (in_cart == null)
				in_cart = false;
			if (!in_cart) {
				_jq.imCookie("imShopPage", window.location.href, { path: '/' });
				location.href = "cart/index.html";
			}
			else {			
				location.href = "index.html";
			}
		}
		
		return false;
	},
	
	gotoCategory: function (id) {
		_jq.imCookie("imShopPage", window.location.href);
		location.href = "cart/index.html?category=" + id;
	},

	// Continue shopping button
	continueShopping: function () {
		var oldPage = _jq.imCookie("imShopPage", null, { path: '/' });
		if (oldPage != null) {
			window.location.href = oldPage;
		}
		else
			window.location.href = x5engine.imCart.settings.continue_shopping_page;
	},

	// Add a product quantity to the cart
	// If force is true, quantity overrides the actual quantity in the cart. If force is false the quantity is added to the existing one 
	addProduct: function (id, quantity, option, force, message) {
		if (message == null) message = true;
		if (quantity == null) quantity = 1;
		if (force == null) force = false;
		if (option == null) option = "null";

		if (x5engine.imCart.products[id].min_quantity != null && quantity < x5engine.imCart.products[id].min_quantity && quantity != 0) {
			alert((x5engine.l10n.getLocalization("cart_err_quantity")).replace("[QUANTITY]", x5engine.imCart.products[id].min_quantity));
			return false;
		}

		var products = x5engine.imCart._getCart();
		if (products == null && quantity > 0) {
			x5engine.imCart._setCart([{
				"id": id,
				"quantity": quantity,
				"option": escape(option)
			}]);
		} else {
			var found = false;
			for (var i = 0; i < products.length; i++) {
				if (products[i].id == id && unescape(products[i].option) == unescape(option)) {
					found = true;
					if (!force) 
						products[i].quantity = products[i].quantity * 1 + quantity * 1;
					else {
						if (quantity > 0) products[i].quantity = quantity * 1;
						else if (message && confirm(x5engine.l10n.getLocalization("cart_remove_q"))) products[i] = null;
						else if (!message) products[i] = null;
					}
				}
			}
			
			if (!found && quantity > 0) products[products.length] = {
				"id": id,
				"quantity": quantity,
				"option": escape(option)
			};

			x5engine.imCart._setCart(products);
		}
	},

	// Add a product to the cart
	addToCart: function (id, quantity, in_cart, option) {
		var product = x5engine.imCart.products[id];
		if (quantity == null) 
			quantity = _jq("#" + x5engine.imCart.costants.QUANT_FIELD_NAME.replace("{id}", id)).val();
		if (quantity == null) 
			quantity = 1;
		if (option == null) {
			if (_jq("#" + x5engine.imCart.costants.OPT_FIELD_NAME.replace("{id}", id)).length > 0) 
				option = _jq("#" + x5engine.imCart.costants.OPT_FIELD_NAME.replace("{id}", id)).val();
			else
				option = "null";
		}
		
		if (in_cart == null) 
			in_cart = false;

		if (/^[0-9]+$/.test(quantity) == false) {
			alert(x5engine.l10n.getLocalization("cart_err_qty"));
			return false;
		}

		x5engine.imCart.addProduct(id, quantity, option);
		x5engine.imCart.updateWidget();
		x5engine.imCart.gotoCart(in_cart);
	},

	// Remove a product from the cart
	removeFromCart: function (id, option, obj) {
		x5engine.imCart.addProduct(id, 0, option, true);
		x5engine.imCart.updateWidget();
		x5engine.imCart.showCart(obj, 1);
	},

	// Update the quantity of a product in the cart
	updateCart: function (id, obj, updateScreen) {
		if (updateScreen == null) 
			updateScreen = false;
		var quantity = _jq(obj).val();
		if (/^[0-9]+$/.test(quantity) == false) {
			alert(x5engine.l10n.getLocalization("cart_err_qty"));
			return false;
		}
		var products = x5engine.imCart._getCart();
		var option = "null";
		var total_quantity = 0;
		var total_weight = 0;
		
		for (var i = 0; i < products.length; i++) {
			if (products[i].id == id) {
				option = products[i].option;
				total_weight += x5engine.imCart.products[id].weight * quantity;
				total_quantity += quantity;
			}
			else {
				total_weight += x5engine.imCart.products[id].weight * products[i].quantity;
				total_quantity += products[i].quantity;
			}
		}

		x5engine.imCart.addProduct(id, quantity, option, true);
		x5engine.imCart.updateWidget();
		if (updateScreen) 
			x5engine.imCart.showCart(null, 0);
		
		x5engine.imCart.updateTotalPrice(total_quantity, total_weight);
	},

	// Update the widget
	updateWidget: function () {
		var products = x5engine.imCart._getCart();
		var sum = 0;
		var total_vat = 0;
		var total = 0;
		
		_jq(".widget_quantity_total").each(function () {
			if (products != null) {
				for (var i = 0; i < products.length; i++) {
					if (x5engine.imCart.products[products[i].id] != null)
						sum += products[i].quantity;
				}
			}
			_jq(this).html(sum);
		});
		_jq(".widget_amount_total").each(function () {
			if (products != null) {
				for (var i = 0; i < products.length; i++) {
					var id = products[i].id;
					if (x5engine.imCart.products[id] != null) {
						var quantity = products[i].quantity;
						var option = products[i].option;
						var discounts = x5engine.imCart.products[id].discounts;
						var weight = x5engine.imCart.products[id].weight;
						if (weight == null) weight = 0;
						var discount = 0;
						var subtot = 0;
						var vat;

						if (x5engine.imCart.products[id].vat != null) 
							vat = x5engine.imCart.products[id].vat;
						else if (x5engine.imCart.settings.vat != null) 
							vat = x5engine.imCart.settings.vat;
						else
							vat = 0;

						if (discounts != null) 
							discount = x5engine.imCart._getValueFromKey(quantity, discounts);
							
						subtot = quantity * x5engine.imCart.products[id].price;
						subtot -= (subtot * discount);

						total_vat += subtot + subtot * vat;
					}
				}
				sum = total_vat;
			}			
			_jq(this).html(x5engine.imCart._setCurrency(sum));
		});	
	},

	// Empty the cart
	emptyCart: function (getConfirmation) {
		if (getConfirmation == null) getConfirmation = true;
		if (getConfirmation == true) {
			if (confirm(x5engine.l10n.getLocalization("cart_empty"))) 
				x5engine.imCart._realEmptyCart();
		} else
			x5engine.imCart._realEmptyCart();
		return false;
	},

	// Show a category
	showCategory: function (id, obj) {
		obj = obj || "#imCartContainer";
		_jq(obj).empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step1') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div>" + x5engine.l10n.getLocalization('cart_step1_descr') + "<br /><br /><br />");
		var html = "<table id=\"imCartProductsTable\"><tr class=\"imCartHeader\">";
		html += "<td>" + x5engine.l10n.getLocalization("cart_descr") + "</td>";
		html += "<td>" + x5engine.l10n.getLocalization("cart_opt") + "</td>";
		html += "<td>" + x5engine.l10n.getLocalization("cart_price") + "</td>";
		if (!x5engine.imCart.settings.vatincluded)
			html += "<td>" + x5engine.l10n.getLocalization("cart_vat") + "</td>";
		html += "<td>" + x5engine.l10n.getLocalization("cart_qty") + "</td>";
		html += "</tr>";
		var str_eval = "";
		for (var pid in x5engine.imCart.products) {
			var product = x5engine.imCart.products[pid];
			if (product.category == id) {
				html += "<tr><td><b>" + product.name + "</b><br />" + product.description + "</td>";
				html += "<td>";
				if (product.options != null && product.options.length > 0) {
					html += "<select id=\"product_" + pid + "_opt\">";
					for (var option in product.options)
					html += "<option value=\"" + product.options[option] + "\">" + x5engine.imCart._restoreSpecialChars(unescape(product.options[option])) + "</option>";
					html += "</select>";
				}
				html += "</td>";
				html += "<td style=\"text-align: right;\">" + x5engine.imCart._setCurrency(product.price) + "</td>";
				if (!x5engine.imCart.settings.vatincluded)
					html += "<td style=\"text-align: right;\">" + ((product.vat != null && product.vat > 0) ? (product.vat * 100).toFixed(2) + "%" : " - ") + "</td>";
				html += "<td style=\"text-align: right;\"><input type=\"text\" value=\"1\" id=\"product_" + pid + "_qty\" size=\"3\" style=\"text-align: right\"/>";
				html += "<img id=\"buy_" + pid + "\" style=\"cursor: pointer; vertical-align: middle; margin-left: 5px;\" src=\"images/cart-add.gif\" alt=\"" + x5engine.l10n.getLocalization("cart_add") + "\" title=\"" + x5engine.l10n.getLocalization("cart_add") + "\" /></td>";
				str_eval += "_jq('#buy_" + pid + "').unbind('click').click(function () { x5engine.imCart.addToCart('" + pid + "', null, true); });";
				html += "</tr>";
			}
		}

		html += "</table>";
		html += "<div style=\"text-align: center; margin-top: 20px;\">";
		html += "<input type=\"button\" id=\"imCartButtonBack\" value=\"" + x5engine.l10n.getLocalization("cart_continue_shopping") + "\" style=\"margin-right: 5px;\" />";
		html += "<input type=\"button\" id=\"imCartButtonNext\" value=\"" + x5engine.l10n.getLocalization("cart_goshop") + "\" style=\"margin-right: 5px;\" />";
		html += "</div>";

		_jq(obj).append(html);
		eval(str_eval);
		_jq("#imCartButtonBack").unbind("click").click(x5engine.imCart.continueShopping);
		_jq("#imCartButtonNext").unbind("click").click(function () { x5engine.imCart.showCart(); });
	},

	// Show the cart
	showCart: function (obj, time) {
		var email_data = {};
		var str_eval = "";

		_jq(".imTip").fadeOut(100, function () {
			_jq(".imTip").remove();
		});

		obj = obj || "#imCartContainer";
		obj = _jq(obj);
		if (time == null) time = 100;

		var products = x5engine.imCart._getCart();
		var filtered_products = new Array();
		// Filter the products (if the cookie contains products not in the cart anymore)
		for (var i = 0; i < products.length; i++) {
			if (x5engine.imCart.products[products[i].id] != null)
				filtered_products[filtered_products.length] = products[i];
		}
		products = filtered_products;
		
		if (products != null && products.length > 0) {
			obj.fadeOut(time, function () {
				obj.empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step2') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div>");
				
				var html = x5engine.l10n.getLocalization('cart_step2_cartlist') + "<br /><br />";
				
				// Products
				html += "<table id=\"imCartProductsTable\"><tr class=\"imCartHeader\">";
				html += "<td>" + x5engine.l10n.getLocalization("cart_descr") + "</td>";
				html += "<td>" + x5engine.l10n.getLocalization("cart_opt") + "</td>";
				html += "<td>" + x5engine.l10n.getLocalization("cart_price") + "</td>";
				if (!x5engine.imCart.settings.vatincluded)
					html += "<td>" + x5engine.l10n.getLocalization("cart_vat") + "</td>";
				html += "<td>" + x5engine.l10n.getLocalization("cart_qty") + "</td>";
				html += "<td>" + x5engine.l10n.getLocalization("cart_subtot") + "</td><td></td></tr>";
				var total = 0;
				var total_vat = 0;
				var shipping_total = 0;
				var shipping_total_vat = 0;
				var total_quantity = 0;
				var total_weight = 0;		
			
				for (var i = 0; i < products.length; i++) {
					var id = products[i].id;
					var quantity = products[i].quantity;
					var option = products[i].option;
					var discounts = x5engine.imCart.products[id].discounts;
					var weight = x5engine.imCart.products[id].weight;
					if (weight == null) weight = 0;
					var discount = 0;
					var subtot = 0;
					var vat;

					if (x5engine.imCart.products[id].vat != null) 
						vat = x5engine.imCart.products[id].vat;
					else if (x5engine.imCart.settings.vat != null) 
						vat = x5engine.imCart.settings.vat;
					else
						vat = 0;

					if (discounts != null) 
						discount = x5engine.imCart._getValueFromKey(quantity, discounts);

					total_weight += weight * quantity;
					total_quantity += quantity;
					subtot = quantity * x5engine.imCart.products[id].price;
					subtot -= (subtot * discount);

					total += subtot;
					total_vat += subtot + subtot * vat;

					var cart_p = x5engine.imCart.products[id];
					email_data[id + "_" + option] = {
						id_user: cart_p.id_user,
						name: cart_p.name,
						description: cart_p.description,
						single_price: x5engine.imCart._setCurrency(x5engine.imCart.products[id].price - x5engine.imCart.products[id].price * discount),
						price: x5engine.imCart._setCurrency(subtot),
						price_vat: x5engine.imCart._setCurrency(subtot + subtot * vat),
						option: option,
						quantity: quantity,
						vat: vat,
						vat_f: x5engine.imCart._setCurrency(vat * (x5engine.imCart.products[id].price - x5engine.imCart.products[id].price * discount))
					};

					html += "<tr><td>" + x5engine.imCart.products[id].id_user + (x5engine.imCart.products[id].description != "" ? " - " + x5engine.imCart.products[id].description : "") + "</td>";
					html += "<td>";
					if (option != "null") {
						var freeid = "product_" + id + "_" + x5engine.utils.imHash(option) + "_opt";
						html += "<select id=\"" + freeid + "\">";
						for (var j = 0; j < x5engine.imCart.products[id].options.length; j++) {
							html += "<option value=\"" + x5engine.imCart.products[id].options[j] + "\" " + ((x5engine.imCart.products[id].options[j] == unescape(option)) ? "selected" : "") + ">" + x5engine.imCart._restoreSpecialChars(unescape(x5engine.imCart.products[id].options[j])) + "</option>";
							str_eval += "_jq('#" + freeid + "').unbind('change').change(function () {x5engine.imCart._updateOption('" + id + "', '" + option + "', _jq('#" + freeid + "').val() , " + quantity + ", '" + _jq(obj).attr("id") + "')});";
						}
						html += "</select>";
					}
					html += "</td>";
					html += "<td style=\"text-align: right;\">" + x5engine.imCart._setCurrency(x5engine.imCart.products[id].price - x5engine.imCart.products[id].price * discount) + "</td>";
					if (!x5engine.imCart.settings.vatincluded)
						html += "<td style=\"text-align: right;\">" + ((vat) ? (vat * 100).toFixed(2) + "%" : "-") + "</td>";
					html += "<td style=\"text-align: right;\"><input style=\"text-align: right;\" id=\"qty_product_" + i + "\" type=\"text\" value=\"" + quantity + "\" size=\"2\"></td>";
					str_eval += "_jq('#qty_product_" + i + "').unbind('change').change(function () {x5engine.imCart.updateCart('" + id + "', this, true);});";
					html += "<td style=\"text-align: right;\">" + x5engine.imCart._setCurrency(subtot + subtot * vat) + "</td>"; 
					html += "<td style=\"text-align: center\"><img id=\"remove_product_" + i + "\" style=\"vertical-align: middle; cursor: pointer;\" src=\"../cart/images/cart-remove.gif\" alt=\"" + x5engine.l10n.getLocalization("cart_remove") + "\" title=\"" + x5engine.l10n.getLocalization("cart_remove") + "\" /></td></tr>";
					str_eval += "_jq('#remove_product_" + i + "').click(function () { return x5engine.imCart.removeFromCart('" + id + "', '" + option + "', '#" + _jq(obj).attr('id') + "'); });";
				}
				
				x5engine.imCart.product_price_plus_vat = total_vat;

				if (x5engine.imCart.email_data == null) x5engine.imCart.email_data = {};
				x5engine.imCart.email_data.products = email_data;

				html += "<tr style=\"text-align: right;\"><td style=\"border-width: 0; background-color: transparent;\" colspan=\"4\"></td><td class=\"nostyle\">" + x5engine.l10n.getLocalization("cart_total") + "</td><td class=\"nostyle\">" + x5engine.imCart._setCurrency(total) + "</td></tr>";
				if (!x5engine.imCart.settings.vatincluded)
					html += "<tr style=\"text-align: right;\"><td style=\"border-width: 0; background-color: transparent;\" colspan=\"4\"></td><td class=\"nostyle\">" + x5engine.l10n.getLocalization("cart_total_vat") + "</td><td class=\"nostyle\">" + x5engine.imCart._setCurrency(total_vat) + "</td></tr>";

				html += "</table>";
				html += "<input type=\"hidden\" id=\"imInputTotalPrice\" name=\"imInputTotalPrice\" value=\"" + total_vat + "\"/>";

				// Payment
				html += "<br />" + x5engine.l10n.getLocalization('cart_step2_shiplist') + "<br /><br /><table id=\"imCartPaymentsTable\">";
				html += "<tr class=\"imCartHeader\"><td colspan=\"2\">" + x5engine.l10n.getLocalization('cart_payment') + "</td><td>" + x5engine.l10n.getLocalization("cart_price") + "</td></tr>";
				var payments = x5engine.imCart.payments;

				for (var i = 0; i < payments.length; i++) {
					var price = payments[i].price || 0;
					if (payments[i].vat != null) price += price * payments[i].vat;
					html += "<tr><td style=\"width: 5%\"><input type=\"radio\" name=\"imPaymentType\" value=\"" + i + "\" id=\"cart_payment_" + i + "\" " + ((x5engine.imCart._getPayment() == i && x5engine.imCart.email_data.payment != null) ? "checked" : "") + "/></td><td style=\"width: 80%\"><label for=\"cart_payment_" + i + "\"><b>" + payments[i].name + ":</b> " + payments[i].description + "</label></td><td style=\"width: 15%; text-align: right;\">" + ((price > 0) ? x5engine.imCart._setCurrency(price) : "-") + "</td></tr>";
					str_eval += "_jq('#cart_payment_" + i + "').unbind('click').click(function () { x5engine.imCart.updatePayment(" + i + ", " + total_quantity + ", " + total_weight + ")});";
					if (x5engine.imCart._getPayment() == i) total_vat += price;
				}
				html += "</table>";
				
				// Shipping
				html += "<br /><table id=\"imCartShippingsTable\">";
				html += "<tr class=\"imCartHeader\"><td colspan=\"2\">" + x5engine.l10n.getLocalization('cart_shipping') + "</td><td>" + x5engine.l10n.getLocalization("cart_price") + "</td></tr>";
				var shippings = x5engine.imCart.shippings;

				for (var i = 0; i < shippings.length; i++) {
					var price = x5engine.imCart._getShippingPrice(shippings[i], x5engine.imCart.product_price_plus_vat, total_quantity, total_weight);
					html += "<tr><td style=\"width: 5%\"><input type=\"radio\" name=\"imShippingType\" value=\"" + i + "\" id=\"cart_shipping_" + i + "\" " + ((x5engine.imCart._getShipping() == i && x5engine.imCart.email_data.shipping != null) ? "checked" : "") + "/></td><td style=\"width: 80%\"><label for=\"cart_shipping_" + i + "\"><b>" + shippings[i].name + ":</b> " + shippings[i].description + "</label></td><td style=\"width: 15%; text-align: right;\">" + ((price > 0) ? x5engine.imCart._setCurrency(price) : "-") + "</td></tr>";
					str_eval += "_jq('#cart_shipping_" + i + "').unbind('click').click(function () {x5engine.imCart.updateShipping(" + i + ", " + total_quantity + ", " + total_weight + ");});";
					if (x5engine.imCart._getShipping() == i) 
						total_vat += price;
				}
				html += "</table><br />";
				
				// Price summary
				html += "<div><div id=\"imCartTotalPriceCont\">" + x5engine.l10n.getLocalization('cart_total_price') + ": <span id=\"imDivTotalPrice\">" + x5engine.imCart._setCurrency(total_vat) + "</span></div></div>";
				
				// Currency conversion disable
				if (x5engine.utils.isOnline() && false) {
					var currencies = x5engine.imCart.settings.currencies;
					if (currencies != null && currencies.length > 0) {
						html += "<select id=\"imCurrencyConversion\" style=\"margin-left: 10px\">";
						str_eval += "_jq('#imCurrencyConversion').unbind('change').change(function () { x5engine.imCart.currencyConversion(this); });";
						html += "<option value=\"\">" + x5engine.l10n.getLocalization("cart_currency_conversion") + "</option>";
						for (i = 0; i < currencies.length; i++) {
							html += "<option value=\"" + currencies[i].value + "\">" + currencies[i].value + " - " + currencies[i].text + "</option>";
						}
						html += "</select><div id=\"currencyConvResult\">Risultato conversione</div>";
					}
				}
				
				html += "<br /><br /><br />";
				html += "<div style=\"text-align: center; margin-top: 30px;\">";
				html += "<input type=\"button\" id=\"imCartButtonBack\" value=\"" + x5engine.l10n.getLocalization("cart_continue_shopping") + "\" style=\"margin-right: 5px;\" />";
				html += "<input type=\"button\" id=\"imCartButtonEmpty\" value=\"" + x5engine.l10n.getLocalization("cart_empty_button") + "\" style=\"margin: 0 5px;\" />";
				html += "<input type=\"button\" id=\"imCartButtonNext\" value=\"" + x5engine.l10n.getLocalization("cart_gonext") + "\" style=\"margin-left: 5px;\"/>";
				html += "</div>";
			
				obj.append(html).fadeIn(time);
				_jq("#imCartButtonBack").unbind("click").click(x5engine.imCart.continueShopping);
				_jq("#imCartButtonEmpty").unbind("click").click(function () { x5engine.imCart.emptyCart(true); });
				_jq("#imCartButtonNext").unbind("click").click(function () { x5engine.imCart.showForm('#' + _jq(obj).attr("id")); });
				eval(str_eval);
				_jq("#imInputTotalPrice").val(total_vat);
			});
		} else {
			obj.fadeOut(time, function () {
				obj.empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step2') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div><br />");
				obj.append("<div style=\"text-align: center; font-weight: bold; font-size: 12px;\">" + x5engine.l10n.getLocalization("cart_err_emptycart") + "</div><br /><br />");
				obj.append("<div style=\"text-align: center\"><input type=\"button\" id=\"imCartButtonBack\" value=\"" + x5engine.l10n.getLocalization("cart_continue_shopping") + "\" style=\"margin-right: 5px;\" /></div>");
				_jq("#imCartButtonBack").unbind("click").click(x5engine.imCart.continueShopping);
				obj.fadeIn(time);
			});
		}

		return false;
	},

	// Convert currency using Google
	currencyConversion: function (obj) {
		if (obj.selectedIndex != 0) {
			if (x5engine.imCart.email_data.clear_total != null) {
				_jq.ajax({
					url: x5engine.imCart.settings.post_url + "?action=currency&amount=" + x5engine.imCart.email_data.clear_total + "&from=" + x5engine.imCart.settings.currency_id + "&to=" + _jq(obj).val(),
					type: "GET",
					dataType: "json",
					success: function (data) {
						_jq("#currencyConvResult").html(x5engine.imCart._setCurrency(data.value, x5engine.imCart.settings.currency_format, _jq(obj).val()));
					},
					error: function () {
						_jq("#currencyConvResult").html(x5engine.l10n.getLocalization("cart_err_currency_conversion"));
					}
				});
			} else {
				obj.selectedIndex = 0;
			}
		} else {
			_jq("#currencyConvResult").empty();
		}
	},

	// Show the user data form
	showForm: function (obj) {
		if (x5engine.imCart._getPayment() == null) {
			alert(x5engine.l10n.getLocalization("cart_err_payment"));
			return false;
		}
		if (x5engine.imCart._getShipping() == null) {
			alert(x5engine.l10n.getLocalization("cart_err_shipping"));
			return false;
		}
		
		if (x5engine.imCart.settings.minimum_amount > 0 && 	_jq("#imInputTotalPrice").val() * 1 < x5engine.imCart.settings.minimum_amount) {
			alert(x5engine.l10n.getLocalization('cart_err_minimum_price').replace(/\[PRICE\]/g, x5engine.imCart._setCurrency(x5engine.imCart.settings.minimum_amount)));
			return false;
		}
		
		obj = obj || "#imCartContainer";
		obj = _jq(obj);
		
		obj.fadeOut(100, function () {
			obj.empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step3') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div>");

			var html = x5engine.l10n.getLocalization('cart_step3_descr') + "<br /><br />"; 
			html += "<form id=\"imCartUserDataForm\">" + x5engine.imCart.html_form + "<br />";
			html += "<div style=\"text-align: center\">";
			html += "<input type=\"button\" id=\"imCartButtonBack\" value=\"" + x5engine.l10n.getLocalization("cart_goback") + "\" style=\"margin-right: 5px;\"/>";
			html += "<input type=\"button\" id=\"imCartUserFormSubmit\" value=\"" + x5engine.l10n.getLocalization("cart_gonext") + "\" style=\"margin-right: 5px;\" /></form>";
			html += "</div>";
			
			obj.append(html).fadeIn(100);
			
			_jq("#imCartButtonBack").unbind("click").click(function () { x5engine.imCart.saveForm(obj); x5engine.imCart.showCart('#' + obj.attr("id")); });
			_jq("#imCartUserFormSubmit").unbind("click").click(function () { x5engine.imCart.showOrderSummary('#' + obj.attr("id")); });
			
			if (x5engine.imCart.settings.form_autocomplete) {
				var json = x5engine.imCart._getFormData();
				if (json != null) 
					for (var i = 0; i < json.length; i++)
						_jq("#" + json[i].id).val(json[i].val);
			}
		});
	},
	
	saveForm: function (obj) {
		var fields = _jq("#imCartUserDataForm");
		var post = {};
		var form = [];
		_jq(fields).find(":input").each(function () {
			if (_jq(this).attr("id") != null && _jq(this).attr("id") != "" && !_jq(this).is("label") && _jq(this).attr("type") != "submit" && _jq(this).attr("type") != "button" && _jq(this).attr("type") != "reset") {
				var fieldName = _jq("label[for=" + _jq(this).attr("id") + "] span").html();
				try {
					if (fieldName.charAt(fieldName.length - 1) == ":") fieldName = fieldName.substr(0, fieldName.length - 1);
					post[_jq(this).attr("id")] = {
						name: fieldName,
						imValue: (_jq(this).is(":checkbox") ? (_jq(this).prop("checked") ? "yes" : "no") : x5engine.imCart._htmlOutput(_jq(this).val()))
					};
					form[form.length] = {
						"id": _jq(this).attr("id"),
						"val": (_jq(this).is(":checkbox") ? (_jq(this).prop("checked") ? "yes" : "no") : x5engine.imCart._htmlOutput(_jq(this).val()))
					};
				} catch (e) {}
			}
		});

		if (x5engine.imCart.settings.form_autocomplete) 
			x5engine.imCart._setFormData(form);
		x5engine.imCart.email_data.form = post;
	},

	// Show the order summary
	showOrderSummary: function (obj) {
		obj = obj || "#imCartContainer";
		obj = _jq(obj);
		if (x5engine.imForm.validate("#imCartUserDataForm", {
			type: x5engine.imCart.settings.form_validation,
			showAll: true,
			position: "right"
		})) {
			obj.fadeOut(100, function () {
				var post;
				x5engine.imCart.saveForm(obj);
				post = x5engine.imCart.email_data.form;

				obj.empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step4') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div>");
				
				// Summary
				// Products
				var html = x5engine.l10n.getLocalization('cart_step4_descr') + "<br /><br />"; 
				html += "<table id=\"imCartProductsTable\"><tr class=\"imCartHeader\">";
				html += "<td style=\"width: " + (x5engine.imCart.settings.vatincluded ? "48" : "35") + "%\">" + x5engine.l10n.getLocalization("cart_descr") + "</td>";
				html += "<td style=\"width: 13%\">" + x5engine.l10n.getLocalization("cart_opt") + "</td>";
				html += "<td style=\"width: 13%\">" + x5engine.l10n.getLocalization("cart_qty") + "</td>";
				html += "<td style=\"width: 13%\">" + x5engine.l10n.getLocalization("cart_price") + "</td>";
				if (!x5engine.imCart.settings.vatincluded)
					html += "<td style=\"width: 13%\">" + x5engine.l10n.getLocalization("cart_vat") + "</td>";
				html += "<td style=\"width: 13%\">" + x5engine.l10n.getLocalization("cart_subtot") + "</td></tr>";

				var data = x5engine.imCart.email_data;

				for (var product in data.products) {
					html += "<tr valign=\"top\"><td>" + data.products[product].name + (data.products[product].description != "" ? " - " + data.products[product].description : "") + "</td>";
					html += "<td>" + ((data.products[product].option != "null") ? x5engine.imCart._restoreSpecialChars(unescape(data.products[product].option)) : "") + "</td>";
					html += "<td style=\"text-align: right;\">" + data.products[product].quantity + "</td>";
					html += "<td style=\"text-align: right;\">" + data.products[product].single_price + "</td>";
					if (!x5engine.imCart.settings.vatincluded)
						html += "<td style=\"text-align: right;\">" + ((data.products[product].vat != null && data.products[product].vat > 0) ? ((data.products[product].vat * 100).toFixed(2) + "% / " + data.products[product].vat_f) : "-") + "</td>";
					html += "<td style=\"text-align: right;\">" + data.products[product].price_vat + "</td></tr>";
				}
				html += "</table>";

				// Payment
				var payment = x5engine.imCart.payments[x5engine.imCart._getPayment()]
				html += "<br /><table id=\"imCartPaymentsTable\" style=\"width: 74%\">";
				html += "<tr class=\"imCartHeader\"><td>" + x5engine.l10n.getLocalization("cart_payment") + "</td>" + ((x5engine.imCart.email_data.payment.price != null) ? "<td>" + x5engine.l10n.getLocalization("cart_price") + "</td>" : "") + "</tr>";
				html += "<tr valign=\"top\"><td style=\"width: 85%\"><b>" + payment.name + "</b><br />" + payment.description + "</td>" + ((x5engine.imCart.email_data.payment.price != null) ? "<td style=\"width: 15%; text-align: right;\">" + x5engine.imCart.email_data.payment.price + "</td>" : "") + "</tr></table>";
				
				// Shipping
				var shippig = x5engine.imCart.shippings[x5engine.imCart._getShipping()]
				html += "<br /><table id=\"imCartShippingsTable\" style=\"width: 74%\">";
				html += "<tr class=\"imCartHeader\"><td style=\"width: 85%\">" + x5engine.l10n.getLocalization("cart_shipping") + "</td>" + ((x5engine.imCart.email_data.shipping.price != null) ? "<td style=\"width: 15%\">" + x5engine.l10n.getLocalization("cart_price") + "</td>" : "") + "</tr>";
				html += "<tr valign=\"top\"><td><b>" + shippig.name + "</b><br />" + shippig.description;
				html += "<br /><br /><b>" + x5engine.l10n.getLocalization("cart_shipping_address") + ":</b><br />";
				if (post["imCartName_shipping"] != null) {
					html += ((post["imCartCompany_shipping"] != null && post["imCartCompany_shipping"] != "") ? post["imCartCompany_shipping"].imValue + " - " : "");
					html += ((post["imCartName_shipping"] != null) ? post["imCartName_shipping"].imValue + " " : "");
					html += ((post["imCartLastName_shipping"] != null) ? post["imCartLastName_shipping"].imValue : "");
					html += " " + ((post["imCartEmail_shipping"] != null) ? ("(" + post["imCartEmail_shipping"].imValue + ")") : "") + "<br />";
					html += ((post["imCartAddress1_shipping"] != null) ? (post["imCartAddress1_shipping"].imValue + "<br />") : "");
					html += ((post["imCartAddress2_shipping"] != null) ? (post["imCartAddress2_shipping"].imValue + "<br />") : "");
					html += ((post["imCartCity_shipping"] != null) ? (post["imCartCity_shipping"].imValue) : "");
					html += ((post["imCartStateRegion_shipping"] != null) ? (" (" + post["imCartStateRegion_shipping"].imValue + ")") : "");
					html += ((post["imCartZipPostalCode_shipping"] != null) ? (", " + post["imCartZipPostalCode_shipping"].imValue + "<br />") : "");
					html += ((post["imCartCountry_shipping"] != null) ? (post["imCartCountry_shipping"].imValue + "<br />") : "");
				} else if (post["imCartName"] != null) {
					html += ((post["imCartCompany"] != null && post["imCartCompany"] != "") ? post["imCartCompany"].imValue + " - " : "");
					html += ((post["imCartName"] != null) ? post["imCartName"].imValue + " " : "");
					html += ((post["imCartLastName"] != null) ? post["imCartLastName"].imValue : "");
					html += " " + ((post["imCartEmail"] != null) ? ("(" + post["imCartEmail"].imValue + ")") : "") + "<br />";
					html += ((post["imCartAddress1"] != null) ? (post["imCartAddress1"].imValue + "<br />") : "");
					html += ((post["imCartAddress2"] != null) ? (post["imCartAddress2"].imValue + "<br />") : "");
					html += ((post["imCartCity"] != null) ? (post["imCartCity"].imValue) : "");
					html += ((post["imCartStateRegion"] != null) ? (" (" + post["imCartStateRegion"].imValue + ")") : "");
					html += ((post["imCartZipPostalCode"] != null) ? (", " + post["imCartZipPostalCode"].imValue + "<br />") : "");
					html += ((post["imCartCountry"] != null) ? (post["imCartCountry"].imValue + "<br />") : "");
				}
				html += "</td>" + ((x5engine.imCart.email_data.shipping.price != null) ? "<td style=\"text-align: right\">" + x5engine.imCart.email_data.shipping.price + "</td>" : "") + "</tr></table>";

				// Total
				html += "<div><div id=\"imCartTotalPriceCont\">" + x5engine.l10n.getLocalization('cart_total_price') + ": <span id=\"imDivTotalPrice\">" + x5engine.imCart.email_data.total + "</span></div></div>";

				// Next/Previous button
				html += "<br /><br /><br />";
				html += "<div style=\"text-align: center; clear: both; margin-top: 30px;\">";
				html += "<input type=\"button\" id=\"imCartButtonBack\" value=\"" + x5engine.l10n.getLocalization("cart_goback") + "\" style=\"margin-right: 5px;\" />";
				html += "<input type=\"button\" id=\"imCartUserFormSubmit\" value=\"" + x5engine.l10n.getLocalization("cart_gonext") + "\" style=\"margin-left: 5px;\" />";
				html += "</html>";

				obj.append(html).fadeIn(100);
				
				_jq("#imCartButtonBack").unbind("click").click(function () { x5engine.imCart.showForm('#' + obj.attr("id")); });
				_jq("#imCartUserFormSubmit").unbind("click").click(function () { x5engine.imCart.sendOrderEmail('#' + obj.attr("id")); });
			});

		} else _jq("#imCartUserFormSubmit").attr("disabled", false);
	},

	// Send the email and shows the payment
	sendOrderEmail: function (obj) {
		_jq("#imCartUserFormSubmit").attr("disabled", true);
		if (x5engine.imCart.email_data.order_no == null || x5engine.imCart.email_data.order_no == "")
			x5engine.imCart.email_data.order_no = x5engine.imCart._createOrderNo();
		var pid = x5engine.imCart._getPayment();
		if (x5engine.imCart.payments[pid].html != null)
			x5engine.imCart.email_data.payment.html = x5engine.imCart._createPaymentHtml(x5engine.imCart.payments[pid].html);
		if (x5engine.utils.isOnline()) {
			_jq("#imCartUserFormSubmit").val(x5engine.l10n.getLocalization('cart_order_process')).css({ cursor: "wait" });
			_jq("#imCartButtonBack").remove();
			_jq.ajax({
				type: "POST",
				url: x5engine.imCart.settings.post_url,
				data: x5engine.imCart.email_data,
				success: function (a) {
					x5engine.imCart.showPayment(obj);
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					x5engine.imCart.showPayment(obj);
				}
			});
		} else {
			x5engine.utils.showOfflineMessage(x5engine.l10n.getLocalization('cart_err_offline_email').replace(/\[MAIL\]/g, x5engine.imCart.email_data.form.imCartEmail.imValue)); 
			x5engine.imCart.showPayment(obj);
		}
	},

	// Shows the payment method
	showPayment: function (obj) {
		var payment = x5engine.imCart.payments[x5engine.imCart._getPayment()];
		_jq(obj).empty().prepend("<h2 id=\"imPgTitle\">" + x5engine.l10n.getLocalization('cart_step5') + "</h2>\n<div style=\"height: 15px;\">&nbsp;</div>");
		var html = x5engine.l10n.getLocalization('cart_step5_descr') + "<br /><br />"; 
		html += "<div id=\"imCartOrderNumber\">" + x5engine.imCart.email_data.order_no + "</div>";
		html += "<h3>" + payment.name + "</h3>" + payment.description + "<br /><br />";
		html += payment.email;
		
		if (payment.html != null) {
			html += "<br /><br /><div style=\"text-align: center\">" + x5engine.imCart._createPaymentHtml(payment.html) + "</div>";
		}

		_jq(obj).fadeOut(100, function () {
			_jq(obj).append(html).fadeIn(100, function () {
				x5engine.imCart.emptyCart(false);
				x5engine.imCart.email_data = null;
				x5engine.imCart._setShipping(null);
				x5engine.imCart._setPayment(null);
			});
		});
	},

	// Update the shipping type and price
	updateShipping: function (n, total_quantity, total_weight) {
		x5engine.imCart._setShipping(n);
		x5engine.imCart.updateTotalPrice(total_quantity, total_weight);
	},

	// Update the payment type and the price
	updatePayment: function (n, total_quantity, total_weight) {
		x5engine.imCart._setPayment(n);
		x5engine.imCart.updateTotalPrice(total_quantity, total_weight);
	},

	// Update the total price
	updateTotalPrice: function (total_quantity, total_weight) {
		var price = x5engine.imCart.product_price_plus_vat;
		var pid;
		var sid;
		var shipping_price = 0;
		var payment_price = 0;
		x5engine.imCart.email_data.total_quantity = total_quantity;
		x5engine.imCart.email_data.total_weight = total_weight;
		if (x5engine.imCart._getShipping() != null) {
			sid = x5engine.imCart._getShipping();
			shipping_price += x5engine.imCart._getShippingPrice(x5engine.imCart.shippings[sid], x5engine.imCart.product_price_plus_vat, total_quantity, total_weight) * 1;
			x5engine.imCart.email_data.shipping = {
				id: sid,
				price: x5engine.imCart._setCurrency(shipping_price),
				name: x5engine.imCart.shippings[sid].name,
				description: x5engine.imCart.shippings[sid].description,
				email: x5engine.imCart.shippings[sid].email
			}
			if (shipping_price == 0) {
				x5engine.imCart.email_data.shipping.price = null;
			}
		}
		if (x5engine.imCart._getPayment() != null) {
			pid = x5engine.imCart._getPayment();
			if (x5engine.imCart.payments[pid].price != null) {
				payment_price += x5engine.imCart.payments[pid].price;
				if (x5engine.imCart.payments[pid].vat != null) payment_price += x5engine.imCart.payments[pid].price * x5engine.imCart.payments[pid].vat;
			}
			x5engine.imCart.email_data.payment = {
				id: pid,
				price: x5engine.imCart._setCurrency(payment_price),
				name: x5engine.imCart.payments[pid].name,
				description: x5engine.imCart.payments[pid].description,
				email: x5engine.imCart.payments[pid].email
			};
			if (x5engine.imCart.payments[pid].html != null)
				x5engine.imCart.email_data.payment.html = x5engine.imCart._createPaymentHtml(x5engine.imCart.payments[pid].html);
			if (payment_price == 0) {
				x5engine.imCart.email_data.payment.price = null;
			}
		}
		price += shipping_price + payment_price;
		x5engine.imCart.email_data.total = x5engine.imCart._setCurrency(price);
		x5engine.imCart.email_data.clear_total = price;
		_jq("#imDivTotalPrice").html(x5engine.imCart._setCurrency(price));
		x5engine.imCart.total_price = price;
	}
});