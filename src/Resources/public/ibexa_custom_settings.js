({177:function(){function e(t){return e="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},e(t)}var t=this;function n(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function r(t,n,r){return(n=function(t){var n=function(t,n){if("object"!==e(t)||null===t)return t;var r=t[Symbol.toPrimitive];if(void 0!==r){var o=r.call(t,n||"default");if("object"!==e(o))return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===n?String:Number)(t)}(t,"string");return"symbol"===e(n)?n:String(n)}(n))in t?Object.defineProperty(t,n,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[n]=r,t}var o=function(e,t,n){t.parentNode.parentNode.querySelector('input[name$="[value]"], textarea[name$="[value]"]').value=n.map((function(e){return e.id})).join(","),c()},i=function(e){e.preventDefault();var i=document.querySelector('form[name="location_settings"]'),a=e.target.closest("button"),u=a.nextElementSibling,l=JSON.parse(a.dataset.udwConfig),f=u.value.split(",").map((function(e){return parseInt(e)})).filter(Number.isInteger);ReactDOM.render(React.createElement(eZ.modules.UniversalDiscovery,function(e){for(var t=1;t<arguments.length;t++){var o=null!=arguments[t]?arguments[t]:{};t%2?n(Object(o),!0).forEach((function(t){r(e,t,o[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(o)):n(Object(o)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(o,t))}))}return e}({onConfirm:o.bind(t,i,a),onCancel:function(){return c()},selectedLocations:f},l)),document.getElementById("react-udw"))},c=function(){return ReactDOM.unmountComponentAtNode(document.getElementById("react-udw"))},a=function(e){var t=e.target.closest("button").parentNode.parentNode;t.parentNode.removeChild(t)},u=function(e){Array.from(e.querySelectorAll("tbody tr")).forEach((function(e){var t=e.querySelector("button.location-selector-browse");t&&(t.removeEventListener("click",i),t.addEventListener("click",i));var n=e.querySelector("button.ibexa-settings-form-remove");n&&(n.removeEventListener("click",a),n.addEventListener("click",a))}))};window.addEventListener("load",(function(){var e=document.getElementById("ibexa-settings-form-add");e&&(e.addEventListener("click",(function(t){t.preventDefault();var n=e.parentElement.previousElementSibling,r=n.querySelector("tbody"),o=document.createElement("tr");o.innerHTML=r.dataset.prototype.replace(/__name__/g,r.dataset.index),r.appendChild(o),r.dataset.index++,u(n)})),u(e.parentElement.previousElementSibling))}))}})[177]();