// set function parseTime,formatTime to filter
export { parseTime, formatTime } from '@/utils';
import { getCurrencySeparator } from '@/utils/auth';

export function pluralize(time, label) {
  if (time === 1) {
    return time + label;
  }
  return time + label + 's';
}

export function timeAgo(time) {
  const between = Date.now() / 1000 - Number(time);
  if (between < 3600) {
    return pluralize(~~(between / 60), ' minute');
  } else if (between < 86400) {
    return pluralize(~~(between / 3600), ' hour');
  } else {
    return pluralize(~~(between / 86400), ' day');
  }
}
// left pad to 0000
export function leftPad(number, targetLength) {
  var output = number + '';
  while (output.length < targetLength) {
    output = '0' + output;
  }
  return output;
}

// slice first letter
export function sliceFirst(name){
  return name.slice(0, 1);
}

export function currencyFormat(num) {
  num = parseFloat(num);
  const currency_sep = getCurrencySeparator();
  return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ').replace('.', currency_sep);
}

/* Number formating*/
export function numberFormatter(num, digits) {
  const si = [
    { value: 1E18, symbol: 'E' },
    { value: 1E15, symbol: 'P' },
    { value: 1E12, symbol: 'T' },
    { value: 1E9, symbol: 'G' },
    { value: 1E6, symbol: 'M' },
    { value: 1E3, symbol: 'k' },
  ];
  for (let i = 0; i < si.length; i++) {
    if (num >= si[i].value) {
      return (num / si[i].value + 0.1).toFixed(digits).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, '$1') + si[i].symbol;
    }
  }
  return num.toString();
}

export function toThousandFilter(num) {
  return (+num || 0).toString().replace(/^-?\d+/g, m => m.replace(/(?=(?!\b)(\d{3})+$)/g, ','));
}

export function uppercaseFirst(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}
export function uppercaseAll(string) {
  return string.toUpperCase();
}

// document totals filter
export function totals(document){
  const tax = document.vatrate;
  const stamp = document.stamprate;

  let total_ht = 0;
  let total_tax = 0;
  let total_stamp = 0;
  let total_discount = 0;
  // items

  document.items.forEach(item => {
    let amount = 0;
    // set item amount
    amount = item.unit_price * item.quantity;
    item.amount = amount;

    // total ht
    total_ht += amount;

    // taxes
    if (tax > 0){
      total_tax += amount * tax / 100;
    }
    // stamp
    if (stamp > 0){
      total_stamp += amount * stamp / 100;
    }
    // total discount
    if (item.discount > 0){
      total_discount += amount * item.discount / 100;
    }
  });

  // set document totals
  document.total_ht = total_ht;
  document.total_tax = total_tax;
  document.total_stamp = total_stamp;
  document.total_discount = total_discount;
  document.total_net = total_ht + total_tax + total_stamp - total_discount;

  return document;
}

// document net total only
export function total_net(document){
  const tax = document.vatrate;
  const stamp = document.stamprate;

  let total_ht = 0;
  let total_tax = 0;
  let total_discount = 0;
  // items

  document.items.forEach(item => {
    let amount = 0;
    // set item amount
    amount = item.unit_price * item.quantity;
    item.amount = amount;

    // total ht
    total_ht += amount;

    // taxes
    if (tax > 0){
      total_tax += amount * tax / 100;
    }
    // stamp
    if (stamp > 0){
      total_tax += amount * stamp / 100;
    }
    // total discount
    if (item.discount > 0){
      total_discount += amount * item.discount / 100;
    }
  });

  // set document net
  return total_ht + total_tax - total_discount;
}

// document code format
export function codeFormat(document, type){
  // const type = document.type;
  const code = document.code;
  const year = document.year;

  return type[0].toUpperCase() + '-' + year + '-' + leftPad(code, 4);
}
