import siteInfo from '@/siteinfo.js';

/**
 * 静态资源地址
 *
 * 资源统一放在后端站点根目录的 /assets/ 下，
 * 域名取自 siteinfo.js 的 siteroot（即后端接口域名），不写死。
 *
 * 例：assetUrl('admin/anmo/technician/no_data.png')
 *  -> https://<后端域名>/assets/admin/anmo/technician/no_data.png
 */
export default function assetUrl(path) {
	const base = String((siteInfo && siteInfo.siteroot) || '').split('/index.php')[0];
	return `${base}/assets/${String(path).replace(/^\/+/, '')}`;
}
