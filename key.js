import fs from 'fs';
import path from 'path';

const ipMap = new Map();      
const usedCodes = new Map();  
const RESERVE_MS = 30 * 60 * 1000; // 30 dakika

let codesCache = null;
function loadCodes() {
  if (codesCache) return codesCache;
  try {
    const p = path.join(process.cwd(), 'codes.txt');
    const txt = fs.readFileSync(p, 'utf8');
    codesCache = txt.split('\n').map(s => s.trim()).filter(Boolean);
    return codesCache;
  } catch (e) {
    console.error('Could not read codes.txt', e);
    return [];
  }
}

function now() { return Date.now(); }

function pickAvailableCode() {
  const codes = loadCodes();
  if (!codes.length) return null;
  for (let i = 0; i < codes.length; i++) {
    const c = codes[Math.floor(Math.random() * codes.length)];
    const taken = usedCodes.get(c);
    if (!taken || (now() - taken) > RESERVE_MS) {
      usedCodes.set(c, now());
      return c;
    }
  }
  return null;
}

export default function handler(req, res) {
  const xff = req.headers['x-forwarded-for'] || '';
  const ip = typeof xff === 'string' ? xff.split(',')[0].trim() : 'unknown';
  const expireBefore = now() - RESERVE_MS;

  for (const [code, ts] of usedCodes) if (ts < expireBefore) usedCodes.delete(code);
  for (const [k, v] of ipMap) if (v.ts < expireBefore) ipMap.delete(k);

  const existing = ipMap.get(ip);
  if (existing && (now() - existing.ts) < RESERVE_MS) {
    const remaining = Math.ceil((RESERVE_MS - (now() - existing.ts)) / 60000);
    return res.status(200).json({ code: existing.code, remainingMinutes: remaining });
  }

  const code = pickAvailableCode();
  if (!code) return res.status(200).json({ error: 'No available codes. Try again later.' });

  ipMap.set(ip, { code, ts: now() });
  usedCodes.set(code, now());

  return res.status(200).json({ code, remainingMinutes: Math.ceil(RESERVE_MS / 60000) });
}
