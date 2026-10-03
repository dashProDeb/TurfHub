/**
 * api-client.js — Centralized fetch wrapper for all PHP API calls
 * 
 * Usage:
 *   import { apiGet, apiPost, apiUpload } from './api-client.js';
 *   
 *   const data = await apiGet('auth/session.php');
 *   const result = await apiPost('auth/login.php', { email, password });
 *   const upload = await apiUpload('turfs/upload-photo.php', formData);
 */

const API_BASE = './backend/api';

/**
 * GET request to a PHP API endpoint
 * @param {string} endpoint - Path relative to API_BASE (e.g., 'auth/session.php')
 * @returns {Promise<Object>} Parsed JSON response
 */
async function apiGet(endpoint) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        credentials: 'include'    // send session cookie
    });
    if (!res.ok) {
        const err = await res.json().catch(() => ({ error: 'Request failed' }));
        throw new Error(err.error || 'Request failed');
    }
    return res.json();
}

/**
 * POST request with JSON body to a PHP API endpoint
 * @param {string} endpoint - Path relative to API_BASE
 * @param {Object} body - JSON-serializable request body
 * @returns {Promise<Object>} Parsed JSON response
 */
async function apiPost(endpoint, body = {}) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });
    if (!res.ok) {
        const err = await res.json().catch(() => ({ error: 'Request failed' }));
        throw new Error(err.error || 'Request failed');
    }
    return res.json();
}

/**
 * POST request with FormData (for file uploads)
 * @param {string} endpoint - Path relative to API_BASE
 * @param {FormData} formData - FormData object with files
 * @returns {Promise<Object>} Parsed JSON response
 */
async function apiUpload(endpoint, formData) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        method: 'POST',
        credentials: 'include',
        body: formData   // FormData (no Content-Type header — browser sets multipart boundary)
    });
    if (!res.ok) {
        const err = await res.json().catch(() => ({ error: 'Upload failed' }));
        throw new Error(err.error || 'Upload failed');
    }
    return res.json();
}

/**
 * DELETE request to a PHP API endpoint
 * @param {string} endpoint - Path relative to API_BASE
 * @param {Object} body - JSON-serializable request body
 * @returns {Promise<Object>} Parsed JSON response
 */
async function apiDelete(endpoint, body = {}) {
    const res = await fetch(`${API_BASE}/${endpoint}`, {
        method: 'DELETE',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    });
    if (!res.ok) {
        const err = await res.json().catch(() => ({ error: 'Request failed' }));
        throw new Error(err.error || 'Request failed');
    }
    return res.json();
}

// Export for both module and script usage
if (typeof window !== 'undefined') {
    window.apiGet = apiGet;
    window.apiPost = apiPost;
    window.apiUpload = apiUpload;
    window.apiDelete = apiDelete;
}
