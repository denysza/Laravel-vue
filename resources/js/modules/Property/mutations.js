export const state = {
  properties: {},
};

export const getters = {
  properties: state => state.properties,
  findProperty: state => property_id => {
    return state.properties.find(property => property.id === property_id);
  },
};

export const mutations = {
  properties(state, value) {
    state.properties = value;
  },
};