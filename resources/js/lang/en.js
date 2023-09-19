export default {
  app: {
    tagline: 'Stock management made easy.',
    recentdocs: 'Pending documents',
    profitSummery: 'Profit summary',
  },
  configuration: {
    company: {
      company: 'Company',
      company_name: 'Name',
      company_sector: 'Activity',
      company_description: 'Description',
      company_address: 'Address',
      company_region: 'Region',
      company_country: 'Country',
      company_tel: 'Telephone',
      company_fax: 'Fax',
      company_email: 'Email',
    },
    invoicing: {
      invoicing: 'Invoicing',
      company_ai: 'TN',
      company_rc: 'TR',
      company_nif: 'TIN',
      company_nis: 'SIN',
      company_rib: 'RIB',
      display_stamp: 'Display stamp',
    },
    currency: {
      currency_name: 'Currency',
      currency_separator: 'Decimal separator',
    },
    saved: 'Configuration saved.',
    yes: 'Yes',
    no: 'No',
  },
  route: {
    dashboard: 'Dashboard',
    contacts: 'Contacts',
    people: 'People',
    companies: 'Companies',
    viewcontact: 'View contact',
    viewcompany: 'View company',
    documents: 'Documents',
    newdocument: 'New document',
    editdocument: 'Edit document',
    viewdocument: 'View document',
    stocks: 'Stocks',
    pos: 'POS',
    purchases: 'Purchases',
    sales: 'Sales',
    viewpurchase: 'View purchase',
    newpurchase: 'New purchase',
    viewsale: 'View sale',
    newsale: 'New sale',
    storehouses: 'Storehouses',
    viewstorehouse: 'View storehouse',
    newstorehouse: 'New storehouse',
    products: 'Products',
    newproduct: 'New product',
    viewproduct: 'View product',
    editproduct: 'Edit product',
    productsList: 'All products',
    categories: 'Categories',
    pagePermission: 'Page Permission',
    rolePermission: 'Role Permission',
    page401: '401',
    page404: '404',
    administrator: 'Administration',
    users: 'Users',
    permission: 'Permission',
    configuration: 'Settings',
    userProfile: 'Profile',
  },
  navbar: {
    logOut: 'Log Out',
    dashboard: 'Dashboard',
    size: 'Global Size',
    profile: 'Profile',
    lang: 'Select language',
    fullscreen: 'Fullscreen',
  },
  login: {
    title: 'Login',
    logIn: 'Log in',
    username: 'Username',
    password: 'Password',
    any: 'any',
    thirdparty: 'Or connect with',
    thirdpartyTips: 'Can not be simulated on local, so please combine you own business simulation! ! !',
    email: 'Email',
  },
  permission: {
    addRole: 'New Role',
    editPermission: 'Edit Permission',
    roles: 'Your roles',
    switchRoles: 'Switch roles',
    tips: 'In some cases it is not suitable to use v-role/v-permission, such as element Tab component or el-table-column and other asynchronous rendering dom cases which can only be achieved by manually setting the v-if with checkRole or/and checkPermission.',
    delete: 'Delete',
    confirm: 'Confirm',
    cancel: 'Cancel',
  },
  table: {
    description: 'Description',
    name: 'Name',
    title: 'Title',
    importance: 'Imp',
    type: 'Type',
    search: 'Search',
    filter: 'Filter',
    add: 'Add',
    export: 'Export',
    id: 'ID',
    date: 'Date',
    status: 'Status',
    actions: 'Actions',
    edit: 'Edit',
    view: 'View',
    cancel: 'Cancel',
    delete: 'Delete',
    validate: 'Validate',
    terminate: 'Terminate',
    save: 'Save',
    print: 'Print',
    confirm: 'Confirm',
    keyword: 'Keyword',
    draft: 'Draft',
    role: 'Role',
    empty: 'Clear basket',
    close: 'Close',
    from: 'From',
    to: 'To',
  },
  nodata: 'No data to diplay.',
  contacts: {
    name: 'Name',
    type: 'Type',
    all: 'TOUT',
    mobile: 'Mobile',
    email: 'Email',
    tel: 'Tel.',
    fax: 'Fax',
    company: 'Company',
    address: 'Address',
    region: 'Region',
    function: 'Fonction',
    yes: 'Yes, delete',
    no: 'Cancel',
    quitWithoutSaving: 'Quit without saving the current document?',
    confirmDelete: 'This will permanantly delete {name}.',
    deleteSucces: '{name} has been deleted from contact list.',
    contactAdded: '{name} has been added to contact list',
    contactUpdated: '{name} contact has been updated.',
    dialogTitleNew: 'Add contact',
    dialogTitleUpdate: 'Edit contact',
    nameRequired: 'Name is required',
    mobileRequired: 'Mobile number is required',
    emailRequired: 'Email is required',
    emailIncorrect: 'Please input correct email address',
    funcRequired: 'Function is required',
    addTag: 'Add tag',
    tag: 'Tag',
  },
  companies: {
    confirmDelete: 'This will permanantly delete {name}.',
    deleteSucces: '{name} has been deleted.',
    dialogAdd: 'Add Company',
    dialogEdit: 'Edit Company',
    name: 'Name',
    tel: 'Tel.',
    fax: 'Fax',
    email: 'Email',
    address: 'Address',
    region: 'Region',
    activity: 'Activity',
    nif: 'TIN',
    rc: 'TR',
    ai: 'TN',
    required: 'This field is required',
    tags_name: 'Tags',
    documents: 'Documents',
    tags: {
      all: 'All',
      client: 'Client',
      supplier: 'Supplier',
      prospect: 'Prospect',
    },
  },
  config: {
    tel: 'Tel.',
    fax: 'Fax',
    address: 'Address',
    region: 'Region',
    email: 'Email',
    capital: 'Capital',
    ai: 'TN',
    rc: 'TR',
    nif: 'TIN',
    nis: 'SIN',
    rib: 'RIB',
  },
  documents: {
    type: 'Type',
    client: 'Client',
    code: 'Code',
    date: 'Date',
    vatrate: 'VAT %',
    stamprate: 'Stamp %',
    description: 'Description',
    unit: 'Unit',
    u_price: 'Unit price',
    qty: 'Quantity',
    amount: 'Amount',
    discount: 'Discount',
    new: 'New document',
    edit: 'Edit document',
    terminate: 'Terminate',
    cancel: 'Cancel',
    dublicate: 'Dublicate',
    saveDraft: 'Save draft',
    save: 'Save',
    document: 'Document',
    documents: 'Documents',
    items: 'Items',
    newItem: 'Add an item',
    docstatus: 'Status',
    payement: 'Payements',
    observations: 'Observations',
    total_ht: 'TOTAL',
    total_tva: 'VAT',
    total_stamp: 'STAMP',
    total_discount: 'DISCOUNT',
    total_ttc: 'TOTAL',
    total_net: 'NET TO PAY',
    menu: {
      all: 'All',
      estimates: 'Quotations',
      invoices: 'Invoices',
    },
    types: {
      estimate: 'Quotation',
      invoice: 'Invoice',
    },
    status: {
      draft: 'draft',
      pending: 'pending',
      validated: 'accepted',
      canceled: 'refuted',
      finished: 'paied',
      undefined: '',
    },
    confirmDelete: 'This will permanantly delete document <b>{code}</b>.',
    deleteSucces: 'Document <b>{code}</b> has been deleted.',
    added: 'Document <b>{code}</b> added',
    updated: 'Document <b>{code}</b> updated',
    finished: 'Document finished.',
    canceled: 'Document canceled.',
    formErrorMessage: 'The form contains errors, please fill in the necessary fields.',
  },
  products: {
    product: 'Product',
    qty: 'Qty',
    nbProducts: 'Nb. of products',
    cost: 'Cost',
    amount: 'Amount',
    profit: 'Profit',
    estimated: 'Estimated profit',
    name: 'Name',
    category: 'Category',
    categories: 'Categories',
    bar_code: 'Bar code',
    description: 'Description',
    unit_price: 'Purchase U.P',
    unit_sale_price: 'Sale U.P',
    discount: 'Discount (%)',
    min_qty: 'Minimum quantity',
    stock_qty: 'Available quantity',
    storehouse: 'Storehouse',
    total: 'Total',
    totalCost: 'Total cost',
    totalDiscount: 'Total discount',
    totalSalesTitle: 'Sales summary',
    totalPurchasesTitle: 'Purchases summary',
    totalProfit: 'Total profit',
    totalEstimated: 'Estimated profit',
    cat_parent: 'Parent',
    cat_order: 'Order',
    cat_add: 'Add',
    cat_reorder: 'Rearrange',
    reset: 'Reset',
    confirmDelete: 'This will permanantly delete <b> {name} </b>.',
    deleteSucces: '<b> {name} </b> has been deleted from product list.',
    productAdded: '<b> {name} </b> has been added to product list',
    productUpdated: '<b> {name} </b> product has been updated.',
    upload: 'Drop image here or click to upload',
    uploadHelper: 'Click on image to replace it.',
    formErrorMessage: 'The form contains errors, please fill in the necessary fields.',
    manageCategories: 'Manage',
    dragToReorder: 'Drag and drop a category to rearrange it.',
    categoryAdded: 'New category saved.',
    categoryUpdated: 'Category updated.',
    categoryDeleted: 'Category deleted.',
    catDeleteConfim: 'This will permanantly delete <b> {name} </b>.',
    emptyBasket: 'Empty basket',
    saleAdded: 'Sale saved.',
  },
  storehouses: {
    name: 'Nom',
    status: 'Status',
    storestatus: {
      active: 'Active',
      inactive: 'Inactive',
    },
    code: 'Code',
    adress: 'Address',
    users: 'Users',
    nouser: 'None',
    purchase_operations: 'Purchase operations',
    sale_operations: 'Sale operations',
    purchase_total: 'Purchase total',
    sale_total: 'Sale total',
    createTitle: 'Add storehouse',
    editTitle: 'Edit storehouse',
    updated: 'Storehouse updated.',
    created: 'Storehouse created.',
  },
  errorLog: {
    tips: 'Please click the bug icon in the upper right corner',
    description: 'Now the management system are basically the form of the spa, it enhances the user experience, but it also increases the possibility of page problems, a small negligence may lead to the entire page deadlock. Fortunately Vue provides a way to catch handling exceptions, where you can handle errors or report exceptions.',
    documentation: 'Document introduction',
  },
  excel: {
    export: 'Export',
    selectedExport: 'Export Selected Items',
    placeholder: 'Please enter the file name(default excel-list)',
  },
  zip: {
    export: 'Export',
    placeholder: 'Please enter the file name(default file)',
  },
  pdf: {
    tips: 'Here we use window.print() to implement the feature of downloading pdf.',
  },
  theme: {
    change: 'Change Theme',
    documentation: 'Theme documentation',
    tips: 'Tips: It is different from the theme-pick on the navbar is two different skinning methods, each with different application scenarios. Refer to the documentation for details.',
  },
  tagsView: {
    refresh: 'Refresh',
    close: 'Close',
    closeOthers: 'Close Others',
    closeAll: 'Close All',
  },
  settings: {
    title: 'Page style setting',
    theme: 'Theme Color',
    tagsView: 'Open Tags-View',
    fixedHeader: 'Fixed Header',
    sidebarLogo: 'Sidebar Logo',
  },
  user: {
    'role': 'Role',
    'password': 'Password',
    'confirmPassword': 'Confirm password',
    'name': 'Name',
    'email': 'Email',
  },
  roles: {
    description: {
      admin: 'Super Administrator. Have access and full permission to all pages.',
      manager: 'Manager. Have access and permission to most of pages except permission page.',
      editor: 'Editor. Have access to most of pages, full permission with articles and related resources.',
      user: 'Normal user. Have access to some pages',
      visitor: 'Visitor. Have access to static pages, should not have any writable permission',
    },
  },
};
