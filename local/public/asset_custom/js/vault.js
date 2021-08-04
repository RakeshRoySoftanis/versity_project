var Vault = {		
	encrypt: function(plaintext, password){		
		return Zohovault.AES.encrypt(plaintext,password,256);
	},
	decrypt: function(ciphertext, password){		
		return Zohovault.AES.decrypt(ciphertext,password,256);
	},
	fileEncrypt: function(plaintext, password){		
		return CryptoJS.AES.encrypt(plaintext,password).toString();
	},
	fileDecrypt: function(ciphertext, password){		
		return CryptoJS.AES.decrypt(ciphertext,password).toString(CryptoJS.enc.Latin1);
	},
	hash: function(plaintext){
		return Zohovault.hash(plaintext);
	},
	Base64_encode: function(input){
		return Zohovault.Base64.encode(input);
	},
	Base64_decode: function(input){
		return Zohovault.Base64.decode(input);
	},	
	RSA_encrypt: function(plaintext, publicKey){
		var rsa = new RSAKey();
		rsa.setPublic(publicKey, '10001');
		var res = rsa.encrypt(plaintext);	 
		if(res) {
			ciphertext = res; 
		    return ciphertext;		    
		}	
	},
	RSA_decrypt: function(ciphertext, privateKey){		 
		privateKey = privateKey.split(',');
	    	var rsa = new RSAKey();	 
		rsa.setPrivateEx(privateKey[0], privateKey[1], privateKey[2], privateKey[3], privateKey[4], privateKey[5], privateKey[6], privateKey[7]);
		if(ciphertext.length == 0) {
		     return;
		}
		var plaintext = rsa.decrypt(ciphertext);	 
		return plaintext;	   		 
	},	
	PBKDF2_key: function(password, salt, iteration){
		/** SJCL and Crypto-js output compatibility changes
		 *  SJCL <SALT> converting toHex() value.    
		 */		
		function toHex(str) {
			var hex = '';
			for(var i=0;i<str.length;i++) {
				hex += ''+str.charCodeAt(i).toString(16);
			}		
			return hex;
		}
		var hmacSHA256 = function (key) {
	   		var hasher = new sjcl.misc.hmac( key, sjcl.hash.sha256 );
	    	this.encrypt = function () {
	       		return hasher.encrypt.apply( hasher, arguments );
	    	};
		};	
		var passwordSalt = sjcl.codec.hex.toBits(toHex(salt));
		var derivedKey = sjcl.misc.pbkdf2( password, passwordSalt, iteration, 256, hmacSHA256 );
		return sjcl.codec.hex.fromBits( derivedKey );
	}
};
