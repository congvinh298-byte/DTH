export const LAP_VO_MARKET = {
  latitude: 10.357422,
  longitude: 105.522124,
};

export const SERVICE_RADIUS_KM = 15;

function toRadians(value) {
  return (value * Math.PI) / 180;
}

export function distanceFromLapVoMarketKm(coordinates) {
  if (!coordinates) {
    return null;
  }

  const earthRadiusKm = 6371;
  const latitudeDelta = toRadians(coordinates.latitude - LAP_VO_MARKET.latitude);
  const longitudeDelta = toRadians(coordinates.longitude - LAP_VO_MARKET.longitude);
  const firstLatitude = toRadians(LAP_VO_MARKET.latitude);
  const secondLatitude = toRadians(coordinates.latitude);
  const haversine =
    Math.sin(latitudeDelta / 2) ** 2 +
    Math.cos(firstLatitude) * Math.cos(secondLatitude) * Math.sin(longitudeDelta / 2) ** 2;

  return earthRadiusKm * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
}

export function isWithinServiceArea(coordinates) {
  const distance = distanceFromLapVoMarketKm(coordinates);
  return distance !== null && distance <= SERVICE_RADIUS_KM;
}
