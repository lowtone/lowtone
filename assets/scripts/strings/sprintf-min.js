/*!
sprintf() for JavaScript 0.6

Copyright (c) Alexandru Marasteanu <alexaholic [at) gmail (dot] com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of sprintf() for JavaScript nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Alexandru Marasteanu BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
sprintf=(function(){function a(c,b){for(var d=[];b>0;d[--b]=c){}return d.join("")}return function(){var g=0,k,h=arguments[g++],d=[],e,b,j,l,n="";while(h){if(e=/^[^\x25]+/.exec(h)){d.push(e[0])}else{if(e=/^\x25{2}/.exec(h)){d.push("%")}else{if(e=/^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(h)){if(((k=arguments[e[1]||g++])==null)||(k==undefined)){throw ("Too few arguments.")}if(/[^s]/.test(e[7])&&(typeof(k)!="number")){throw ("Expecting number but found "+typeof(k))}switch(e[7]){case"b":k=k.toString(2);break;case"c":k=String.fromCharCode(k);break;case"d":k=parseInt(k);break;case"e":k=e[6]?k.toExponential(e[6]):k.toExponential();break;case"f":k=e[6]?parseFloat(k).toFixed(e[6]):parseFloat(k);break;case"o":k=k.toString(8);break;case"s":k=((k=String(k))&&e[6]?k.substring(0,e[6]):k);break;case"u":k=Math.abs(k);break;case"x":k=k.toString(16);break;case"X":k=k.toString(16).toUpperCase();break}k=(/[def]/.test(e[7])&&e[2]&&k>=0?"+"+k:k);j=e[3]?e[3]=="0"?"0":e[3].charAt(1):" ";l=e[5]-String(k).length-n.length;b=e[5]?a(j,l):"";d.push(n+(e[4]?k+b:b+k))}else{throw ("Huh ?!")}}}h=h.substring(e[0].length)}return d.join("")}})();