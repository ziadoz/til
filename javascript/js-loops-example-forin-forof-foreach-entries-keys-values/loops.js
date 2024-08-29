/**
 * Array Looping
 */
const arr = ['foo'];

// A for...in loop will return the key...
// @see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...in
for (const key in arr) {
    console.log(key);
}

// A for...of loop will return the value...
// @see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...of
for (const val of arr) {
    console.log(val);
}

// A forEach loop can return the value, key and array...
// @see: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/forEach
arr.forEach((val) => console.log(val));
arr.forEach((val, key) => console.log(val, key));
arr.forEach((val, key, all) => console.log(val, key, all));

// Array.entries() will return the key and value...
for (const [key, val] of arr.entries()) {
    console.log(key, val);
}

// Array.keys() and Array.values() will return an iterator...
console.log(arr.keys());
console.log(arr.values());

/**
 * Object Looping
 */
const obj = { foo: 'bar' };

// A for...in loop will return the key...
for (const key in obj) {
    console.log(key);
}

// A for...of loop doesn't work...
// for (const val of obj) {
//     console.log(key);
// }

// A forEach loop doesn't work either...
// obj.forEach((val) => console.log(val));
// obj.forEach((val, key) => console.log(val, key));
// obj.forEach((val, key, all) => console.log(val, key, all));

// Object.entries() will return the key and value...
for (const [key, val] of Object.entries(obj)) {
    console.log(key, val);
}

// Object.keys() and Object.values() will return the keys and values...
console.log(Object.keys(obj));
console.log(Object.values(obj));