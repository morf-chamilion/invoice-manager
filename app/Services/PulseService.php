<?php

namespace App\Services;

use Composer\InstalledVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PulseService
{
	/**
	 * Get installed packages and their dependencies.
	 */
	public function getPackagesAndDependencies(): array
	{
		$requiredPackages = $this->getComposerArray()['require'];

		$latestVersions = $this->getLatestVersions($requiredPackages);

		$packages = [];

		foreach ($requiredPackages as $name => $version) {
			if ($name !== 'php') {
				$packages[] = [
					'name' => $name,
					'active_version' => InstalledVersions::getVersion($name),
					'latest_version' => $latestVersions[$name] ?? InstalledVersions::getVersion($name),
				];
			}
		}

		return $packages;
	}

	/**
	 * Authorization the request headers.
	 */
	public function validateAuthorizationRequest(Request $request): bool
	{
		$token = $request->bearerToken();

		if (!$token) {
			return false;
		}

		if ($this->validateHash($request->getHost(), $token)) {
			return true;
		}

		return false;
	}

	/**
	 * Get the composer file contents.
	 */
	private function getComposerArray(): array
	{
		$json = file_get_contents(base_path('composer.json'));

		return json_decode($json, true);
	}

	/**
	 * Get latest versions of packages from Packagist.
	 */
	private function getLatestVersions(array $requiredPackages): array
	{
		$latestPackages = [];

		foreach ($requiredPackages as $package => $version) {
			if ($package !== 'php') {
				$response = Http::get("https://repo.packagist.org/p/{$package}.json");
				$packageData = $response->json();

				if (isset($packageData['packages'][$package])) {
					$packageRepository = $packageData['packages'][$package];

					$stableVersions = array_filter($packageRepository, function ($key) {
						return strpos($key, 'dev') === false && strpos($key, 'beta') === false;
					}, ARRAY_FILTER_USE_KEY);

					uasort($stableVersions, function ($a, $b) {
						return $a['time'] <=> $b['time'];
					});

					if (!empty($stableVersions)) {
						$latestPackages[$package] = Arr::last($stableVersions)['version_normalized'];
					}
				}
			}
		}

		return $latestPackages;
	}

	/**
	 * Validate authorization hash.
	 */
	private function validateHash(string $plaintext, string $hashedURL): bool
	{
		$calculatedHash = hash('sha256', $plaintext);

		return $calculatedHash === $hashedURL;
	}
}
