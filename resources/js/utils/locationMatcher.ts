import { nigerStateLGAs, type LocalGovernmentArea, type Ward } from '@/data/nigerState';
import { geolocationService } from './geolocation';

export interface LocationMatch {
  lga?: LocalGovernmentArea;
  ward?: Ward;
  distance: number;
  confidence: 'high' | 'medium' | 'low';
}

export interface LocationSuggestion {
  lga: LocalGovernmentArea;
  ward?: Ward;
  distance: number;
  score: number;
}

export class LocationMatcher {
  private static instance: LocationMatcher;
  
  public static getInstance(): LocationMatcher {
    if (!LocationMatcher.instance) {
      LocationMatcher.instance = new LocationMatcher();
    }
    return LocationMatcher.instance;
  }

  /**
   * Find the closest LGA to given coordinates
   */
  public findClosestLGA(latitude: number, longitude: number): LocationMatch {
    let closestLGA: LocalGovernmentArea | undefined;
    let minDistance = Infinity;

    for (const lga of nigerStateLGAs) {
      const distance = geolocationService.calculateDistance(
        latitude,
        longitude,
        lga.coordinates.latitude,
        lga.coordinates.longitude
      );

      if (distance < minDistance) {
        minDistance = distance;
        closestLGA = lga;
      }
    }

    // Determine confidence based on distance
    let confidence: 'high' | 'medium' | 'low' = 'low';
    if (minDistance <= 10) {
      confidence = 'high';
    } else if (minDistance <= 25) {
      confidence = 'medium';
    }

    return {
      lga: closestLGA,
      distance: minDistance,
      confidence
    };
  }

  /**
   * Get location suggestions based on coordinates
   */
  public getLocationSuggestions(
    latitude: number,
    longitude: number,
    maxSuggestions: number = 5
  ): LocationSuggestion[] {
    const suggestions: LocationSuggestion[] = [];

    for (const lga of nigerStateLGAs) {
      const distance = geolocationService.calculateDistance(
        latitude,
        longitude,
        lga.coordinates.latitude,
        lga.coordinates.longitude
      );

      // Calculate score based on distance and population (larger LGAs get slight preference)
      const populationFactor = Math.log(lga.population_estimate) / 20;
      const distanceFactor = Math.max(0, 100 - distance);
      const score = distanceFactor + populationFactor;

      suggestions.push({
        lga,
        distance,
        score
      });
    }

    // Sort by score (highest first) and return top suggestions
    return suggestions
      .sort((a, b) => b.score - a.score)
      .slice(0, maxSuggestions);
  }

  /**
   * Check if coordinates are within Niger State boundaries (approximate)
   */
  public isWithinNigerState(latitude: number, longitude: number): boolean {
    // Niger State approximate boundaries
    const bounds = {
      north: 11.8,
      south: 8.2,
      east: 7.8,
      west: 3.0
    };

    return (
      latitude >= bounds.south &&
      latitude <= bounds.north &&
      longitude >= bounds.west &&
      longitude <= bounds.east
    );
  }

  /**
   * Get LGA by coordinates with fallback to closest match
   */
  public async getLGAFromCoordinates(
    latitude: number,
    longitude: number
  ): Promise<LocationMatch> {
    // First check if within Niger State
    if (!this.isWithinNigerState(latitude, longitude)) {
      // If outside Niger State, still find closest LGA but mark as low confidence
      const closest = this.findClosestLGA(latitude, longitude);
      return {
        ...closest,
        confidence: 'low'
      };
    }

    // Find closest LGA within Niger State
    return this.findClosestLGA(latitude, longitude);
  }

  /**
   * Format location for display
   */
  public formatLocation(match: LocationMatch): string {
    if (!match.lga) {
      return 'Unknown location';
    }

    const parts = [match.lga.name];
    
    if (match.ward) {
      parts.unshift(match.ward.name);
    }

    parts.push('Niger State');

    let formatted = parts.join(', ');

    // Add confidence indicator
    if (match.confidence === 'low') {
      formatted += ' (approximate)';
    } else if (match.confidence === 'medium') {
      formatted += ' (nearby)';
    }

    return formatted;
  }

  /**
   * Get all LGAs within a certain radius
   */
  public getLGAsWithinRadius(
    latitude: number,
    longitude: number,
    radiusKm: number
  ): LocalGovernmentArea[] {
    return nigerStateLGAs.filter(lga => {
      const distance = geolocationService.calculateDistance(
        latitude,
        longitude,
        lga.coordinates.latitude,
        lga.coordinates.longitude
      );
      return distance <= radiusKm;
    });
  }

  /**
   * Search LGAs by name or code
   */
  public searchLGAs(query: string): LocalGovernmentArea[] {
    const searchTerm = query.toLowerCase().trim();
    
    if (!searchTerm) {
      return nigerStateLGAs;
    }

    return nigerStateLGAs.filter(lga =>
      lga.name.toLowerCase().includes(searchTerm) ||
      lga.code.toLowerCase().includes(searchTerm) ||
      lga.headquarters.toLowerCase().includes(searchTerm)
    );
  }

  /**
   * Search wards by name or code within an LGA
   */
  public searchWards(query: string, lgaId?: string): Ward[] {
    const searchTerm = query.toLowerCase().trim();
    
    let wards: Ward[] = [];
    
    if (lgaId) {
      const lga = nigerStateLGAs.find(l => l.id === lgaId);
      wards = lga ? lga.wards : [];
    } else {
      wards = nigerStateLGAs.flatMap(lga => lga.wards);
    }

    if (!searchTerm) {
      return wards;
    }

    return wards.filter(ward =>
      ward.name.toLowerCase().includes(searchTerm) ||
      ward.code.toLowerCase().includes(searchTerm)
    );
  }

  /**
   * Get statistics about Niger State LGAs
   */
  public getStatistics() {
    const totalArea = nigerStateLGAs.reduce((sum, lga) => sum + lga.area_km2, 0);
    const totalPopulation = nigerStateLGAs.reduce((sum, lga) => sum + lga.population_estimate, 0);
    const totalWards = nigerStateLGAs.reduce((sum, lga) => sum + lga.wards.length, 0);

    return {
      totalLGAs: nigerStateLGAs.length,
      totalWards,
      totalArea,
      totalPopulation,
      averageArea: totalArea / nigerStateLGAs.length,
      averagePopulation: totalPopulation / nigerStateLGAs.length
    };
  }
}

// Export singleton instance
export const locationMatcher = LocationMatcher.getInstance();
