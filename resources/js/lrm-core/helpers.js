var LRM_Helper = window.LRM_Helper ? window.LRM_Helper : {};
window.LRM_Helper = LRM_Helper;

LRM_Helper.setCookie = function(name,value,days) {
	var expires = "";
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

LRM_Helper.getCookie = function(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

LRM_Helper.PasswordMeter = function( pass1, blacklistArr, pass2 ) {

	function PasswordMeter() {

		this.pass1 = pass1;
		this.pass2 = pass2;
		this.passLength = this.pass1.length;

		this.tokens = {
			letters: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
			numbers: "0123456789",
			specialChars: "!&%/()=?^*+][#><;:,._-|"
		};
		this.letters = this.tokens.letters.split( "" );
		this.numbers = this.tokens.numbers.split( "" );
		this.specialChars = this.tokens.specialChars.split( "" );
	}

	PasswordMeter.prototype = {
		check: function() {
			var self = this;

			if ( self.pass2 && self.pass1 !== self.pass2 ) {
				return 5;
			}

			var val = self.pass1;
			var total = self.passLength;

			var totalLetters = 0;
			var totalNumbers = 0;
			var totalSpecialChars = 0;

			var tokens = val.split( "" );
			var len = tokens.length;
			var i;

			for( i = 0; i < len; ++i ) {
				var token = tokens[i];
				if( self._isLetter( token ) ) {
					totalLetters++;
				} else if( self._isNumber( token ) ) {
					totalNumbers++;
				} else if( self._isSpecialChar( token ) ) {
					totalSpecialChars++;
				}

			}
			
			var result = self._calculate( total, totalLetters, totalNumbers, totalSpecialChars );
			return Math.round(result/2.5);
		},
		_isLetter: function( token ) {
			var self = this;
			if( self.letters.indexOf( token ) == -1 ) {
				return false;
			}
			return true;
		},
		_isNumber: function( token ) {
			var self = this;
			if( self.numbers.indexOf( token ) == -1 ) {
				return false;
			}
			return true;
		},
		_isSpecialChar: function( token ) {
			var self = this;
			if( self.specialChars.indexOf( token ) == -1 ) {
				return false;
			}
			return true;
		},
		_calculate: function( total, letters, numbers, chars ) {
			var level = 0;
			var l = parseInt( letters, 10 );
			var n = parseInt( numbers, 10 );
			var c = parseInt( chars, 10 );

			if( total < 7 ) {
				level += 1;
			}
			if( total >= 7 ) {
				level += 4;
			}

			if( l > 0 ) {
				level += 1;
			}

			if( n > 0 ) {
				level += 2;
			}

			if( c > 0 ) {
				level += 3;
			}

			if ( jQuery.inArray( pass1, blacklistArr ) > 0 ) {
				level = 5;
			}

			return level;
		}
	};

	var pwdMeter = new PasswordMeter();
	return pwdMeter.check();
};
// END
