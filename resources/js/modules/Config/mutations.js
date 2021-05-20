export const state = {
  loaded_select: false,
  select: {},
};

export const getters = {
  loaded_select: state => state.loaded_select,
  select: state => state.select,

  prefecture: state => state.select?.['prefecture'] ?? [],
  sales: state => state.select?.['sales'] ?? [],
  category: state => state.select?.['category'] ?? [],
  property: state => state.select?.['property'] ?? [],
  roof: state => state.select?.['roof'] ?? [],
  wall: state => state.select?.['wall'] ?? [],
  column: state => state.select?.['column'] ?? [],
  priority: state => state.select?.['priority'] ?? [],
  period: state => state.select?.['period'] ?? [],
  Warranty: state => state.select?.['Warranty'] ?? [],
  amount: state => state.select?.['amount'] ?? [],
  constructionperiod: state => state.select?.['constructionperiod'] ?? [],
  completedate: state => state.select?.['completedate'] ?? [],
};

export const mutations = {
  loaded_select(state, value) {
    state.loaded_select = value;
  },
  select(state, value) {
    state.select = value;
  },
};