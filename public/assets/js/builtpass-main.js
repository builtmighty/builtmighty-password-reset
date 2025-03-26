(function($){
	$(document).on('ready', function(){
		class BuiltPass {
			executeFuncs(obj) {
				for (let func in obj) {
					if (obj.hasOwnProperty(func)) {
						obj[func]();
					}
				}
			}

			blockUIOnResetPassword() {
				$('.woocommerce-ResetPassword, .built-password-reset-form').on('submit', function(){
					$.blockUI({
						message: 'Your password is resetting...',
						css: {
							border: 'none',
							padding: '1em',
							backgroundColor: '#000',
							'-webkit-border-radius': '10px',
							'-moz-border-radius': '10px',
							opacity: .5,
							color: '#fff'
						}
					});
				});
			}

			readyFuncs() {
				this.blockUIOnResetPassword();
			}
		}

		const BuiltPassInstance = new BuiltPass();
		BuiltPassInstance.readyFuncs();
	});
})(jQuery);