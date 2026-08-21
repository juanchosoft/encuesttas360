/**
 * Fase B — Device UUID estable + fingerprint (sustituto de MAC).
 */
(function (global) {
  'use strict';

  var STORAGE_KEY = 'e360_device_uuid';

  function simpleHash(str) {
    var hash = 0;
    var s = String(str || '');
    for (var i = 0; i < s.length; i++) {
      hash = ((hash << 5) - hash) + s.charCodeAt(i);
      hash |= 0;
    }
    return 'fp_' + Math.abs(hash);
  }

  function uuidv4() {
    if (global.crypto && typeof global.crypto.randomUUID === 'function') {
      return global.crypto.randomUUID();
    }
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
      var r = (Math.random() * 16) | 0;
      var v = c === 'x' ? r : (r & 0x3) | 0x8;
      return v.toString(16);
    });
  }

  function getOrCreateUuid() {
    var id = '';
    try {
      id = localStorage.getItem(STORAGE_KEY) || '';
    } catch (e) {}
    if (!id) {
      id = 'dev_' + uuidv4();
      try {
        localStorage.setItem(STORAGE_KEY, id);
      } catch (e2) {}
    }
    return id;
  }

  function getFingerprint() {
    var parts = [
      navigator.userAgent || '',
      navigator.platform || '',
      navigator.language || '',
      (screen && screen.width ? screen.width : '') + 'x' + (screen && screen.height ? screen.height : ''),
      (typeof Intl !== 'undefined' && Intl.DateTimeFormat
        ? Intl.DateTimeFormat().resolvedOptions().timeZone
        : '')
    ];
    return simpleHash(parts.join('|'));
  }

  function getMeta() {
    return {
      device_uuid: getOrCreateUuid(),
      device_fingerprint: getFingerprint(),
      device_user_agent: navigator.userAgent || '',
      device_platform: navigator.platform || '',
      device_language: navigator.language || '',
      device_timezone: (typeof Intl !== 'undefined' && Intl.DateTimeFormat
        ? Intl.DateTimeFormat().resolvedOptions().timeZone
        : '')
    };
  }

  function appendToFormData(fd) {
    var m = getMeta();
    Object.keys(m).forEach(function (k) {
      fd.append(k, m[k]);
    });
    return m;
  }

  function appendToUrlSearchParams(params) {
    var m = getMeta();
    Object.keys(m).forEach(function (k) {
      params.set(k, m[k]);
    });
    return m;
  }

  function fillHiddenInputs(root) {
    var scope = root || document;
    var m = getMeta();
    var map = {
      device_token: m.device_uuid,
      device_uuid: m.device_uuid,
      device_fingerprint: m.device_fingerprint,
      device_user_agent: m.device_user_agent,
      device_platform: m.device_platform,
      device_language: m.device_language,
      device_timezone: m.device_timezone
    };
    Object.keys(map).forEach(function (id) {
      var el = scope.getElementById ? scope.getElementById(id) : null;
      if (!el && scope.querySelector) {
        el = scope.querySelector('[name="' + id + '"]');
      }
      if (el) el.value = map[id];
    });
    return m;
  }

  global.DeviceParticipacion = {
    getMeta: getMeta,
    getOrCreateUuid: getOrCreateUuid,
    getFingerprint: getFingerprint,
    appendToFormData: appendToFormData,
    appendToUrlSearchParams: appendToUrlSearchParams,
    fillHiddenInputs: fillHiddenInputs
  };
})(window);
