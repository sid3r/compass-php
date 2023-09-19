import Cookies from 'js-cookie';

const TokenKey = 'isLogged';

export function isLogged() {
  return Cookies.get(TokenKey) === '1';
}

export function setLogged(isLogged) {
  return Cookies.set(TokenKey, isLogged);
}

export function removeToken() {
  return Cookies.remove(TokenKey);
}

// currency
export function setCurrency(config) {
  const currency = config.filter(item => {
    return item.name === 'currency_name';
  });
  const currency_separator = config.filter(item => {
    return item.name === 'currency_separator';
  });
  Cookies.set('currency', currency[0].value);
  Cookies.set('currencySeparator', currency_separator[0].value);
}
export function getCurrency() {
  return Cookies.get('currency');
}
export function getCurrencySeparator() {
  return Cookies.get('currencySeparator');
}
