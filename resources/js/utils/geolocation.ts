export interface GeolocationCoordinates {
  latitude: number;
  longitude: number;
  accuracy?: number;
  altitude?: number | null;
  altitudeAccuracy?: number | null;
  heading?: number | null;
  speed?: number | null;
}

export interface GeolocationPosition {
  coords: GeolocationCoordinates;
  timestamp: number;
}

export interface GeolocationError {
  code: number;
  message: string;
}

export interface LocationResult {
  success: boolean;
  position?: GeolocationPosition;
  error?: GeolocationError;
}

export interface ReverseGeocodeResult {
  address?: string;
  city?: string;
  state?: string;
  country?: string;
  postcode?: string;
  lga?: string;
  ward?: string;
}

export class GeolocationService {
  private static instance: GeolocationService;
  
  public static getInstance(): GeolocationService {
    if (!GeolocationService.instance) {
      GeolocationService.instance = new GeolocationService();
    }
    return GeolocationService.instance;
  }

  /**
   * Check if geolocation is supported by the browser
   */
  public isSupported(): boolean {
    return 'geolocation' in navigator;
  }

  /**
   * Get current position with high accuracy
   */
  public async getCurrentPosition(options?: PositionOptions): Promise<LocationResult> {
    return new Promise((resolve) => {
      if (!this.isSupported()) {
        resolve({
          success: false,
          error: {
            code: -1,
            message: 'Geolocation is not supported by this browser'
          }
        });
        return;
      }

      const defaultOptions: PositionOptions = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 300000 // 5 minutes
      };

      const finalOptions = { ...defaultOptions, ...options };

      navigator.geolocation.getCurrentPosition(
        (position) => {
          resolve({
            success: true,
            position: {
              coords: {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                altitude: position.coords.altitude,
                altitudeAccuracy: position.coords.altitudeAccuracy,
                heading: position.coords.heading,
                speed: position.coords.speed
              },
              timestamp: position.timestamp
            }
          });
        },
        (error) => {
          let message = 'Unknown error occurred';
          switch (error.code) {
            case error.PERMISSION_DENIED:
              message = 'Location access denied by user';
              break;
            case error.POSITION_UNAVAILABLE:
              message = 'Location information is unavailable';
              break;
            case error.TIMEOUT:
              message = 'Location request timed out';
              break;
          }
          
          resolve({
            success: false,
            error: {
              code: error.code,
              message
            }
          });
        },
        finalOptions
      );
    });
  }

  /**
   * Watch position changes
   */
  public watchPosition(
    callback: (result: LocationResult) => void,
    options?: PositionOptions
  ): number | null {
    if (!this.isSupported()) {
      callback({
        success: false,
        error: {
          code: -1,
          message: 'Geolocation is not supported by this browser'
        }
      });
      return null;
    }

    const defaultOptions: PositionOptions = {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 60000 // 1 minute
    };

    const finalOptions = { ...defaultOptions, ...options };

    return navigator.geolocation.watchPosition(
      (position) => {
        callback({
          success: true,
          position: {
            coords: {
              latitude: position.coords.latitude,
              longitude: position.coords.longitude,
              accuracy: position.coords.accuracy,
              altitude: position.coords.altitude,
              altitudeAccuracy: position.coords.altitudeAccuracy,
              heading: position.coords.heading,
              speed: position.coords.speed
            },
            timestamp: position.timestamp
          }
        });
      },
      (error) => {
        let message = 'Unknown error occurred';
        switch (error.code) {
          case error.PERMISSION_DENIED:
            message = 'Location access denied by user';
            break;
          case error.POSITION_UNAVAILABLE:
            message = 'Location information is unavailable';
            break;
          case error.TIMEOUT:
            message = 'Location request timed out';
            break;
        }
        
        callback({
          success: false,
          error: {
            code: error.code,
            message
          }
        });
      },
      finalOptions
    );
  }

  /**
   * Clear position watch
   */
  public clearWatch(watchId: number): void {
    if (this.isSupported()) {
      navigator.geolocation.clearWatch(watchId);
    }
  }

  /**
   * Calculate distance between two coordinates using Haversine formula
   */
  public calculateDistance(
    lat1: number,
    lon1: number,
    lat2: number,
    lon2: number
  ): number {
    const R = 6371; // Earth's radius in kilometers
    const dLat = this.toRadians(lat2 - lat1);
    const dLon = this.toRadians(lon2 - lon1);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(this.toRadians(lat1)) *
        Math.cos(this.toRadians(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  /**
   * Convert degrees to radians
   */
  private toRadians(degrees: number): number {
    return degrees * (Math.PI / 180);
  }

  /**
   * Format coordinates for display
   */
  public formatCoordinates(lat: number, lon: number, precision: number = 6): string {
    return `${lat.toFixed(precision)}, ${lon.toFixed(precision)}`;
  }

  /**
   * Reverse geocoding using a simple service (you can replace with your preferred service)
   */
  public async reverseGeocode(lat: number, lon: number): Promise<ReverseGeocodeResult> {
    try {
      // Using OpenStreetMap Nominatim service (free but rate-limited)
      const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`
      );
      
      if (!response.ok) {
        throw new Error('Geocoding service unavailable');
      }
      
      const data = await response.json();
      
      return {
        address: data.display_name,
        city: data.address?.city || data.address?.town || data.address?.village,
        state: data.address?.state,
        country: data.address?.country,
        postcode: data.address?.postcode,
        lga: data.address?.county || data.address?.local_government,
        ward: data.address?.suburb || data.address?.neighbourhood
      };
    } catch (error) {
      console.error('Reverse geocoding failed:', error);
      return {};
    }
  }
}

// Export singleton instance
export const geolocationService = GeolocationService.getInstance();
