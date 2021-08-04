var Zohovault={};Zohovault.hash=function(s){function safe_add(x,y){var lsw=(65535&x)+(65535&y),msw=(x>>16)+(y>>16)+(lsw>>16);return msw<<16|65535&lsw}function S(X,n){return X>>>n|X<<32-n}function R(X,n){return X>>>n}function Ch(x,y,z){return x&y^~x&z}function Maj(x,y,z){return x&y^x&z^y&z}function Sigma0256(x){return S(x,2)^S(x,13)^S(x,22)}function Sigma1256(x){return S(x,6)^S(x,11)^S(x,25)}function Gamma0256(x){return S(x,7)^S(x,18)^R(x,3)}function Gamma1256(x){return S(x,17)^S(x,19)^R(x,10)}function core_sha256(m,l){var a,b,c,d,e,f,g,h,i,j,T1,T2,K=new Array(1116352408,1899447441,3049323471,3921009573,961987163,1508970993,2453635748,2870763221,3624381080,310598401,607225278,1426881987,1925078388,2162078206,2614888103,3248222580,3835390401,4022224774,264347078,604807628,770255983,1249150122,1555081692,1996064986,2554220882,2821834349,2952996808,3210313671,3336571891,3584528711,113926993,338241895,666307205,773529912,1294757372,1396182291,1695183700,1986661051,2177026350,2456956037,2730485921,2820302411,3259730800,3345764771,3516065817,3600352804,4094571909,275423344,430227734,506948616,659060556,883997877,958139571,1322822218,1537002063,1747873779,1955562222,2024104815,2227730452,2361852424,2428436474,2756734187,3204031479,3329325298),HASH=new Array(1779033703,3144134277,1013904242,2773480762,1359893119,2600822924,528734635,1541459225),W=new Array(64);m[l>>5]|=128<<24-l%32,m[(l+64>>9<<4)+15]=l;for(var i=0;i<m.length;i+=16){a=HASH[0],b=HASH[1],c=HASH[2],d=HASH[3],e=HASH[4],f=HASH[5],g=HASH[6],h=HASH[7];for(var j=0;64>j;j++)W[j]=16>j?m[j+i]:safe_add(safe_add(safe_add(Gamma1256(W[j-2]),W[j-7]),Gamma0256(W[j-15])),W[j-16]),T1=safe_add(safe_add(safe_add(safe_add(h,Sigma1256(e)),Ch(e,f,g)),K[j]),W[j]),T2=safe_add(Sigma0256(a),Maj(a,b,c)),h=g,g=f,f=e,e=safe_add(d,T1),d=c,c=b,b=a,a=safe_add(T1,T2);HASH[0]=safe_add(a,HASH[0]),HASH[1]=safe_add(b,HASH[1]),HASH[2]=safe_add(c,HASH[2]),HASH[3]=safe_add(d,HASH[3]),HASH[4]=safe_add(e,HASH[4]),HASH[5]=safe_add(f,HASH[5]),HASH[6]=safe_add(g,HASH[6]),HASH[7]=safe_add(h,HASH[7])}return HASH}function str2binb(str){for(var bin=Array(),mask=(1<<chrsz)-1,i=0;i<str.length*chrsz;i+=chrsz)bin[i>>5]|=(str.charCodeAt(i/chrsz)&mask)<<24-i%32;return bin}function Utf8Encode(string){string=string.replace(/\r\n/g,"\n");for(var utftext="",n=0;n<string.length;n++){var c=string.charCodeAt(n);128>c?utftext+=String.fromCharCode(c):c>127&&2048>c?(utftext+=String.fromCharCode(c>>6|192),utftext+=String.fromCharCode(63&c|128)):(utftext+=String.fromCharCode(c>>12|224),utftext+=String.fromCharCode(c>>6&63|128),utftext+=String.fromCharCode(63&c|128))}return utftext}function binb2hex(binarray){for(var hex_tab=hexcase?"0123456789ABCDEF":"0123456789abcdef",str="",i=0;i<4*binarray.length;i++)str+=hex_tab.charAt(binarray[i>>2]>>8*(3-i%4)+4&15)+hex_tab.charAt(binarray[i>>2]>>8*(3-i%4)&15);return str}var chrsz=8,hexcase=0;return s=Utf8Encode(s),binb2hex(core_sha256(str2binb(s),s.length*chrsz))},Zohovault.Utf8={encode:function(string){string=string.replace(/\r\n/g,"\n");for(var utftext="",n=0;n<string.length;n++){var c=string.charCodeAt(n);128>c?utftext+=String.fromCharCode(c):c>127&&2048>c?(utftext+=String.fromCharCode(c>>6|192),utftext+=String.fromCharCode(63&c|128)):(utftext+=String.fromCharCode(c>>12|224),utftext+=String.fromCharCode(c>>6&63|128),utftext+=String.fromCharCode(63&c|128))}return utftext},decode:function(utftext){for(var string="",i=0,c=c1=c2=0;i<utftext.length;)c=utftext.charCodeAt(i),128>c?(string+=String.fromCharCode(c),i++):c>191&&224>c?(c2=utftext.charCodeAt(i+1),string+=String.fromCharCode((31&c)<<6|63&c2),i+=2):(c2=utftext.charCodeAt(i+1),c3=utftext.charCodeAt(i+2),string+=String.fromCharCode((15&c)<<12|(63&c2)<<6|63&c3),i+=3);return string}},Zohovault.Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(input){var chr1,chr2,chr3,enc1,enc2,enc3,enc4,output="",i=0;for(input=Zohovault.Utf8.encode(input);i<input.length;)chr1=input.charCodeAt(i++),chr2=input.charCodeAt(i++),chr3=input.charCodeAt(i++),enc1=chr1>>2,enc2=(3&chr1)<<4|chr2>>4,enc3=(15&chr2)<<2|chr3>>6,enc4=63&chr3,isNaN(chr2)?enc3=enc4=64:isNaN(chr3)&&(enc4=64),output=output+this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4);return output},decode:function(input){var chr1,chr2,chr3,enc1,enc2,enc3,enc4,output="",i=0;for(input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");i<input.length;)enc1=this._keyStr.indexOf(input.charAt(i++)),enc2=this._keyStr.indexOf(input.charAt(i++)),enc3=this._keyStr.indexOf(input.charAt(i++)),enc4=this._keyStr.indexOf(input.charAt(i++)),chr1=enc1<<2|enc2>>4,chr2=(15&enc2)<<4|enc3>>2,chr3=(3&enc3)<<6|enc4,output+=String.fromCharCode(chr1),64!=enc3&&(output+=String.fromCharCode(chr2)),64!=enc4&&(output+=String.fromCharCode(chr3));return output=Zohovault.Utf8.decode(output)}},Zohovault.AES={},Zohovault.AES.cipher=function(input,w){for(var Nb=4,Nr=w.length/Nb-1,state=[[],[],[],[]],i=0;4*Nb>i;i++)state[i%4][Math.floor(i/4)]=input[i];state=Zohovault.AES.addRoundKey(state,w,0,Nb);for(var round=1;Nr>round;round++)state=Zohovault.AES.subBytes(state,Nb),state=Zohovault.AES.shiftRows(state,Nb),state=Zohovault.AES.mixColumns(state,Nb),state=Zohovault.AES.addRoundKey(state,w,round,Nb);state=Zohovault.AES.subBytes(state,Nb),state=Zohovault.AES.shiftRows(state,Nb),state=Zohovault.AES.addRoundKey(state,w,Nr,Nb);for(var output=new Array(4*Nb),i=0;4*Nb>i;i++)output[i]=state[i%4][Math.floor(i/4)];return output},Zohovault.AES.keyExpansion=function(key){for(var Nb=4,Nk=key.length/4,Nr=Nk+6,w=new Array(Nb*(Nr+1)),temp=new Array(4),i=0;Nk>i;i++){var r=[key[4*i],key[4*i+1],key[4*i+2],key[4*i+3]];w[i]=r}for(var i=Nk;Nb*(Nr+1)>i;i++){w[i]=new Array(4);for(var t=0;4>t;t++)temp[t]=w[i-1][t];if(i%Nk==0){temp=Zohovault.AES.subWord(Zohovault.AES.rotWord(temp));for(var t=0;4>t;t++)temp[t]^=Zohovault.AES.rCon[i/Nk][t]}else Nk>6&&i%Nk==4&&(temp=Zohovault.AES.subWord(temp));for(var t=0;4>t;t++)w[i][t]=w[i-Nk][t]^temp[t]}return w},Zohovault.AES.subBytes=function(s,Nb){for(var r=0;4>r;r++)for(var c=0;Nb>c;c++)s[r][c]=Zohovault.AES.sBox[s[r][c]];return s},Zohovault.AES.shiftRows=function(s,Nb){for(var t=new Array(4),r=1;4>r;r++){for(var c=0;4>c;c++)t[c]=s[r][(c+r)%Nb];for(var c=0;4>c;c++)s[r][c]=t[c]}return s},Zohovault.AES.mixColumns=function(s){for(var c=0;4>c;c++){for(var a=new Array(4),b=new Array(4),i=0;4>i;i++)a[i]=s[i][c],b[i]=128&s[i][c]?s[i][c]<<1^283:s[i][c]<<1;s[0][c]=b[0]^a[1]^b[1]^a[2]^a[3],s[1][c]=a[0]^b[1]^a[2]^b[2]^a[3],s[2][c]=a[0]^a[1]^b[2]^a[3]^b[3],s[3][c]=a[0]^b[0]^a[1]^a[2]^b[3]}return s},Zohovault.AES.addRoundKey=function(state,w,rnd,Nb){for(var r=0;4>r;r++)for(var c=0;Nb>c;c++)state[r][c]^=w[4*rnd+c][r];return state},Zohovault.AES.subWord=function(w){for(var i=0;4>i;i++)w[i]=Zohovault.AES.sBox[w[i]];return w},Zohovault.AES.rotWord=function(w){for(var tmp=w[0],i=0;3>i;i++)w[i]=w[i+1];return w[3]=tmp,w},Zohovault.AES.sBox=[99,124,119,123,242,107,111,197,48,1,103,43,254,215,171,118,202,130,201,125,250,89,71,240,173,212,162,175,156,164,114,192,183,253,147,38,54,63,247,204,52,165,229,241,113,216,49,21,4,199,35,195,24,150,5,154,7,18,128,226,235,39,178,117,9,131,44,26,27,110,90,160,82,59,214,179,41,227,47,132,83,209,0,237,32,252,177,91,106,203,190,57,74,76,88,207,208,239,170,251,67,77,51,133,69,249,2,127,80,60,159,168,81,163,64,143,146,157,56,245,188,182,218,33,16,255,243,210,205,12,19,236,95,151,68,23,196,167,126,61,100,93,25,115,96,129,79,220,34,42,144,136,70,238,184,20,222,94,11,219,224,50,58,10,73,6,36,92,194,211,172,98,145,149,228,121,231,200,55,109,141,213,78,169,108,86,244,234,101,122,174,8,186,120,37,46,28,166,180,198,232,221,116,31,75,189,139,138,112,62,181,102,72,3,246,14,97,53,87,185,134,193,29,158,225,248,152,17,105,217,142,148,155,30,135,233,206,85,40,223,140,161,137,13,191,230,66,104,65,153,45,15,176,84,187,22],Zohovault.AES.rCon=[[0,0,0,0],[1,0,0,0],[2,0,0,0],[4,0,0,0],[8,0,0,0],[16,0,0,0],[32,0,0,0],[64,0,0,0],[128,0,0,0],[27,0,0,0],[54,0,0,0]],Zohovault.AES.encrypt=function(plaintext,password,nBits){var blockSize=16;if(128!=nBits&&192!=nBits&&256!=nBits)return"";plaintext=Utf8.encode(plaintext),password=Utf8.encode(password);for(var nBytes=nBits/8,pwBytes=new Array(nBytes),i=0;nBytes>i;i++)pwBytes[i]=isNaN(password.charCodeAt(i))?0:password.charCodeAt(i);var key=Zohovault.AES.cipher(pwBytes,Zohovault.AES.keyExpansion(pwBytes));key=key.concat(key.slice(0,nBytes-16));for(var counterBlock=new Array(blockSize),nonce=(new Date).getTime(),nonceMs=nonce%1e3,nonceSec=Math.floor(nonce/1e3),nonceRnd=Math.floor(65535*Math.random()),i=0;2>i;i++)counterBlock[i]=nonceMs>>>8*i&255;for(var i=0;2>i;i++)counterBlock[i+2]=nonceRnd>>>8*i&255;for(var i=0;4>i;i++)counterBlock[i+4]=nonceSec>>>8*i&255;for(var ctrTxt="",i=0;8>i;i++)ctrTxt+=String.fromCharCode(counterBlock[i]);for(var keySchedule=Zohovault.AES.keyExpansion(key),blockCount=Math.ceil(plaintext.length/blockSize),ciphertxt=new Array(blockCount),b=0;blockCount>b;b++){for(var c=0;4>c;c++)counterBlock[15-c]=b>>>8*c&255;for(var c=0;4>c;c++)counterBlock[15-c-4]=b/4294967296>>>8*c;for(var cipherCntr=Zohovault.AES.cipher(counterBlock,keySchedule),blockLength=blockCount-1>b?blockSize:(plaintext.length-1)%blockSize+1,cipherChar=new Array(blockLength),i=0;blockLength>i;i++)cipherChar[i]=cipherCntr[i]^plaintext.charCodeAt(b*blockSize+i),cipherChar[i]=String.fromCharCode(cipherChar[i]);ciphertxt[b]=cipherChar.join("")}var ciphertext=ctrTxt+ciphertxt.join("");return ciphertext=Base64.encode(ciphertext)},Zohovault.AES.decrypt=function(ciphertext,password,nBits){var blockSize=16;if(128!=nBits&&192!=nBits&&256!=nBits)return"";ciphertext=Base64.decode(ciphertext),password=Utf8.encode(password);for(var nBytes=nBits/8,pwBytes=new Array(nBytes),i=0;nBytes>i;i++)pwBytes[i]=isNaN(password.charCodeAt(i))?0:password.charCodeAt(i);var key=Zohovault.AES.cipher(pwBytes,Zohovault.AES.keyExpansion(pwBytes));key=key.concat(key.slice(0,nBytes-16));var counterBlock=new Array(8);ctrTxt=ciphertext.slice(0,8);for(var i=0;8>i;i++)counterBlock[i]=ctrTxt.charCodeAt(i);for(var keySchedule=Zohovault.AES.keyExpansion(key),nBlocks=Math.ceil((ciphertext.length-8)/blockSize),ct=new Array(nBlocks),b=0;nBlocks>b;b++)ct[b]=ciphertext.slice(8+b*blockSize,8+b*blockSize+blockSize);ciphertext=ct;for(var plaintxt=new Array(ciphertext.length),b=0;nBlocks>b;b++){for(var c=0;4>c;c++)counterBlock[15-c]=b>>>8*c&255;for(var c=0;4>c;c++)counterBlock[15-c-4]=(b+1)/4294967296-1>>>8*c&255;for(var cipherCntr=Zohovault.AES.cipher(counterBlock,keySchedule),plaintxtByte=new Array(ciphertext[b].length),i=0;i<ciphertext[b].length;i++)plaintxtByte[i]=cipherCntr[i]^ciphertext[b].charCodeAt(i),plaintxtByte[i]=String.fromCharCode(plaintxtByte[i]);plaintxt[b]=plaintxtByte.join("")}var plaintext=plaintxt.join("");return plaintext=Utf8.decode(plaintext)};var Base64={};Base64.code="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",Base64.encode=function(str,utf8encode){utf8encode="undefined"==typeof utf8encode?!1:utf8encode;var o1,o2,o3,bits,h1,h2,h3,h4,c,plain,coded,e=[],pad="",b64=Base64.code;if(plain=utf8encode?str.encodeUTF8():str,c=plain.length%3,c>0)for(;c++<3;)pad+="=",plain+="\x00";for(c=0;c<plain.length;c+=3)o1=plain.charCodeAt(c),o2=plain.charCodeAt(c+1),o3=plain.charCodeAt(c+2),bits=o1<<16|o2<<8|o3,h1=bits>>18&63,h2=bits>>12&63,h3=bits>>6&63,h4=63&bits,e[c/3]=b64.charAt(h1)+b64.charAt(h2)+b64.charAt(h3)+b64.charAt(h4);return coded=e.join(""),coded=coded.slice(0,coded.length-pad.length)+pad},Base64.decode=function(str,utf8decode){utf8decode="undefined"==typeof utf8decode?!1:utf8decode;var o1,o2,o3,h1,h2,h3,h4,bits,plain,coded,d=[],b64=Base64.code;coded=utf8decode?str.decodeUTF8():str;for(var c=0;c<coded.length;c+=4)h1=b64.indexOf(coded.charAt(c)),h2=b64.indexOf(coded.charAt(c+1)),h3=b64.indexOf(coded.charAt(c+2)),h4=b64.indexOf(coded.charAt(c+3)),bits=h1<<18|h2<<12|h3<<6|h4,o1=bits>>>16&255,o2=bits>>>8&255,o3=255&bits,d[c/4]=String.fromCharCode(o1,o2,o3),64==h4&&(d[c/4]=String.fromCharCode(o1,o2)),64==h3&&(d[c/4]=String.fromCharCode(o1));return plain=d.join(""),utf8decode?plain.decodeUTF8():plain};var Utf8={};Utf8.encode=function(strUni){var strUtf=strUni.replace(/[\u0080-\u07ff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(192|cc>>6,128|63&cc)});return strUtf=strUtf.replace(/[\u0800-\uffff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(224|cc>>12,128|cc>>6&63,128|63&cc)})},Utf8.decode=function(strUtf){var strUni=strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(c){var cc=(15&c.charCodeAt(0))<<12|(63&c.charCodeAt(1))<<6|63&c.charCodeAt(2);return String.fromCharCode(cc)});return strUni=strUni.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(c){var cc=(31&c.charCodeAt(0))<<6|63&c.charCodeAt(1);return String.fromCharCode(cc)})};